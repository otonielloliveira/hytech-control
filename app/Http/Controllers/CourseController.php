<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Models\CourseEnrollment;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Lista todos os cursos disponíveis
     */
    public function index(Request $request)
    {
        $query = Course::published()
            ->with(['certificateType', 'modules', 'enrollments']);

        // Filtro por nível
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filtro por categoria/certificado
        if ($request->filled('certificate_type')) {
            $query->where('certificate_type_id', $request->certificate_type);
        }

        // Busca por título
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filtro por preço
        if ($request->filled('price_filter')) {
            switch ($request->price_filter) {
                case 'free':
                    $query->where('price', 0);
                    break;
                case 'paid':
                    $query->where('price', '>', 0);
                    break;
            }
        }

        // Ordenação
        $sortBy = $request->get('sort', 'featured');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('published_at', 'desc');
                break;
            case 'popular':
                $query->withCount('enrollments')->orderBy('enrollments_count', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')->orderBy('sort_order');
        }

        $courses = $query->paginate(12);

        // Cursos em destaque
        $featuredCourses = Course::published()
            ->featured()
            ->limit(3)
            ->get();

        return view('courses.index', compact('courses', 'featuredCourses'));
    }

    /**
     * Exibe detalhes de um curso específico
     */
    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->published()
            ->with([
                'certificateType',
                'modules.lessons' => function ($query) {
                    $query->published()->orderBy('sort_order');
                }
            ])
            ->firstOrFail();

        $client = Auth::guard('client')->user();
        $enrollment = null;
        $progress = 0;

        if ($client) {
            $enrollment = $course->enrollments()
                ->where('client_id', $client->id)
                ->first();
            
            if ($enrollment) {
                $progress = $enrollment->progress_percentage;
            }
        }

        // Cursos relacionados
        $relatedCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->where(function ($query) use ($course) {
                $query->where('certificate_type_id', $course->certificate_type_id)
                      ->orWhere('level', $course->level);
            })
            ->limit(4)
            ->get();

        return view('courses.show', compact('course', 'enrollment', 'progress', 'relatedCourses'));
    }

    /**
     * Processa a matrícula em um curso
     */
    public function enroll(Request $request, $slug)
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->route('client.login')
                ->with('error', 'Você precisa estar logado para se matricular em um curso.');
        }

        $course = Course::where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Verificar se já está matriculado
        if ($course->isEnrolledBy($client->id)) {
            return redirect()->route('courses.learning', $slug)
                ->with('info', 'Você já está matriculado neste curso.');
        }

        // Verificar vagas disponíveis
        if (!$course->hasAvailableSlots()) {
            return back()->with('error', 'Este curso não possui mais vagas disponíveis.');
        }

        try {
            DB::beginTransaction();

            // Criar matrícula
            $enrollment = CourseEnrollment::create([
                'client_id' => $client->id,
                'course_id' => $course->id,
                'status' => 'active',
                'paid_amount' => $course->getCurrentPrice(),
                'started_at' => now(),
            ]);

            // Se o curso for gratuito, ativar imediatamente
            if ($course->getCurrentPrice() == 0) {
                $enrollment->update(['status' => 'active']);
                DB::commit();
                
                return redirect()->route('courses.learning', $slug)
                    ->with('success', 'Matrícula realizada com sucesso! Bem-vindo ao curso.');
            }

            // Para cursos pagos, redirecionar para pagamento
            DB::commit();
            return redirect()->route('courses.payment', ['slug' => $slug, 'enrollment' => $enrollment->id])
                ->with('success', 'Matrícula iniciada! Complete o pagamento para ter acesso ao curso.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Erro ao processar matrícula. Tente novamente.');
        }
    }

    /**
     * Área de aprendizado do curso
     */
    public function learning($slug)
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->route('client.login');
        }

        $course = Course::where('slug', $slug)
            ->with([
                'modules.lessons.materials',
                'modules.lessons.progress' => function ($query) use ($client) {
                    $query->whereHas('enrollment', function ($q) use ($client) {
                        $q->where('client_id', $client->id);
                    });
                }
            ])
            ->firstOrFail();

        $enrollment = $course->enrollments()
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->firstOrFail();

        // Primeira aula não assistida
        $nextLesson = null;
        foreach ($course->modules as $module) {
            foreach ($module->lessons as $lesson) {
                if (!$lesson->isCompletedBy($enrollment->id)) {
                    $nextLesson = $lesson;
                    break 2;
                }
            }
        }

        return view('courses.learning', compact('course', 'enrollment', 'nextLesson'));
    }

    /**
     * Exibe uma aula específica
     */
    public function lesson($courseSlug, $moduleSlug, $lessonSlug)
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->route('client.login');
        }

        $course = Course::where('slug', $courseSlug)->firstOrFail();
        
        $enrollment = $course->enrollments()
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->firstOrFail();

        $module = $course->modules()
            ->where('slug', $moduleSlug)
            ->firstOrFail();

        $lesson = $module->lessons()
            ->where('slug', $lessonSlug)
            ->with(['materials', 'module.lessons'])
            ->firstOrFail();

        // Verificar se tem acesso à aula
        if (!$lesson->isAccessibleFor($enrollment->id)) {
            return redirect()->route('courses.learning', $courseSlug)
                ->with('error', 'Você precisa completar as aulas anteriores primeiro.');
        }

        // Buscar ou criar progresso da aula
        $progress = LessonProgress::firstOrCreate([
            'course_enrollment_id' => $enrollment->id,
            'course_lesson_id' => $lesson->id,
        ], [
            'started_at' => now(),
        ]);

        // Próxima aula
        $nextLesson = $module->lessons()
            ->where('sort_order', '>', $lesson->sort_order)
            ->orderBy('sort_order')
            ->first();

        // Se não há próxima aula no módulo, buscar no próximo módulo
        if (!$nextLesson) {
            $nextModule = $course->modules()
                ->where('sort_order', '>', $module->sort_order)
                ->orderBy('sort_order')
                ->first();
            
            if ($nextModule) {
                $nextLesson = $nextModule->lessons()->orderBy('sort_order')->first();
            }
        }

        // Aula anterior
        $previousLesson = $module->lessons()
            ->where('sort_order', '<', $lesson->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        return view('courses.lesson', compact(
            'course', 'module', 'lesson', 'enrollment', 'progress',
            'nextLesson', 'previousLesson'
        ));
    }

    /**
     * Marca uma aula como assistida
     */
    public function markLessonCompleted(Request $request, $courseSlug, $moduleSlug, $lessonSlug)
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return response()->json(['error' => 'Não autorizado'], 401);
        }

        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $enrollment = $course->enrollments()
            ->where('client_id', $client->id)
            ->where('status', 'active')
            ->firstOrFail();

        $module = $course->modules()->where('slug', $moduleSlug)->firstOrFail();
        $lesson = $module->lessons()->where('slug', $lessonSlug)->firstOrFail();

        $progress = LessonProgress::where([
            'course_enrollment_id' => $enrollment->id,
            'course_lesson_id' => $lesson->id,
        ])->first();

        if (!$progress) {
            return response()->json(['error' => 'Progresso não encontrado'], 404);
        }

        $watchPercentage = $request->input('watch_percentage', 100);
        $watchDuration = $request->input('watch_duration', 0);

        $progress->updateWatchProgress($watchPercentage, $watchDuration);

        // Atualizar progresso geral do curso
        $enrollment->calculateProgress();

        return response()->json([
            'success' => true,
            'lesson_completed' => $progress->is_completed,
            'course_progress' => $enrollment->fresh()->progress_percentage,
        ]);
    }

    /**
     * Lista cursos do cliente
     */
    public function myCourses()
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->route('client.login');
        }

        $enrollments = $client->courseEnrollments()
            ->with(['course.modules'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses.my-courses', compact('enrollments'));
    }

    /**
     * Baixar certificado
     */
    public function downloadCertificate($slug)
    {
        $client = Auth::guard('client')->user();
        
        if (!$client) {
            return redirect()->route('client.login');
        }

        $course = Course::where('slug', $slug)->firstOrFail();
        $enrollment = $course->enrollments()
            ->where('client_id', $client->id)
            ->where('status', 'completed')
            ->whereNotNull('certificate_issued_at')
            ->firstOrFail();

        // Aqui você implementaria a geração do PDF do certificado
        // Por enquanto, vamos retornar uma view simples
        return view('courses.certificate', compact('course', 'enrollment'));
    }
}
