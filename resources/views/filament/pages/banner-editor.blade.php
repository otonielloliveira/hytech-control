<x-filament-panels::page>
    <div x-data="bannerEditor()" x-init="init()" class="banner-editor-wrapper">
        <!-- Toolbar -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
            <div class="flex items-center gap-4 flex-wrap">
                <h3 class="text-lg font-semibold text-gray-900">Ferramentas</h3>
                
                <button @click="addElement('text')" 
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                    </svg>
                    Adicionar Texto
                </button>

                <button @click="addElement('image')" 
                        class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Adicionar Imagem
                </button>

                <button @click="addElement('button')" 
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path>
                    </svg>
                    Adicionar Botão
                </button>

                <div class="flex-1"></div>

                <button @click="saveDesign()" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Salvar Banner
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4">
            <!-- Canvas Area -->
            <div class="col-span-8">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dimensões do Banner</label>
                        <div class="flex gap-4">
                            <input type="number" x-model="canvasWidth" @change="updateCanvasSize()" 
                                   class="w-32 px-3 py-2 border border-gray-300 rounded-lg" placeholder="Largura">
                            <span class="flex items-center text-gray-500">×</span>
                            <input type="number" x-model="canvasHeight" @change="updateCanvasSize()" 
                                   class="w-32 px-3 py-2 border border-gray-300 rounded-lg" placeholder="Altura">
                        </div>
                    </div>

                    <!-- Canvas -->
                    <div class="relative border-2 border-gray-300 rounded-lg overflow-hidden" 
                         :style="`width: ${canvasWidth}px; height: ${canvasHeight}px; background: ${backgroundColor}`"
                         @click="selectElement(null)">
                        
                        <!-- Background Image -->
                        <div x-show="backgroundImage" 
                             class="absolute inset-0 bg-cover bg-center"
                             :style="`background-image: url(${backgroundImage}); opacity: ${backgroundOpacity / 100}`">
                        </div>

                        <!-- Elements -->
                        <template x-for="(element, index) in elements" :key="index">
                            <div @click.stop="selectElement(index)"
                                 @mousedown="startDrag($event, index)"
                                 :class="{ 'ring-2 ring-blue-500': selectedElement === index }"
                                 class="absolute cursor-move transition-all hover:ring-2 hover:ring-blue-300"
                                 :style="getElementStyle(element)">
                                
                                <!-- Text Element -->
                                <template x-if="element.type === 'text'">
                                    <div contenteditable="true" 
                                         @input="updateElementContent($event, index)"
                                         class="outline-none whitespace-pre-wrap"
                                         x-text="element.content"></div>
                                </template>

                                <!-- Image Element -->
                                <template x-if="element.type === 'image'">
                                    <img :src="element.content" alt="" class="w-full h-full object-cover rounded">
                                </template>

                                <!-- Button Element -->
                                <template x-if="element.type === 'button'">
                                    <button class="px-6 py-3 rounded-lg font-semibold whitespace-nowrap"
                                            :style="`background: ${element.bgColor}; color: ${element.textColor}`"
                                            x-text="element.content"></button>
                                </template>

                                <!-- Resize Handle -->
                                <div x-show="selectedElement === index"
                                     @mousedown.stop="startResize($event, index)"
                                     class="absolute bottom-0 right-0 w-4 h-4 bg-blue-500 cursor-se-resize rounded-tl"></div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Properties Panel -->
            <div class="col-span-4">
                <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Propriedades</h3>

                    <!-- Background Settings -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-700 mb-3">Fundo</h4>
                        
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Cor de Fundo</label>
                            <input type="color" x-model="backgroundColor" 
                                   class="w-full h-10 rounded border border-gray-300">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Imagem de Fundo</label>
                            <input type="text" x-model="backgroundImage" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                   placeholder="URL da imagem">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm text-gray-600 mb-1">Opacidade: <span x-text="backgroundOpacity"></span>%</label>
                            <input type="range" x-model="backgroundOpacity" min="0" max="100"
                                   class="w-full">
                        </div>
                    </div>

                    <!-- Element Properties -->
                    <template x-if="selectedElement !== null && elements[selectedElement]">
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-700 mb-3">Elemento Selecionado</h4>
                            
                            <div class="space-y-3">
                                <!-- Common Properties -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">X</label>
                                        <input type="number" x-model="elements[selectedElement].x"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Y</label>
                                        <input type="number" x-model="elements[selectedElement].y"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Largura</label>
                                        <input type="number" x-model="elements[selectedElement].width"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Altura</label>
                                        <input type="number" x-model="elements[selectedElement].height"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </div>

                                <!-- Text Properties -->
                                <template x-if="elements[selectedElement].type === 'text'">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Tamanho da Fonte</label>
                                        <input type="number" x-model="elements[selectedElement].fontSize"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    </div>
                                </template>

                                <template x-if="elements[selectedElement].type === 'text'">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Cor do Texto</label>
                                        <input type="color" x-model="elements[selectedElement].textColor"
                                               class="w-full h-10 rounded border border-gray-300">
                                    </div>
                                </template>

                                <!-- Button Properties -->
                                <template x-if="elements[selectedElement].type === 'button'">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Cor de Fundo</label>
                                        <input type="color" x-model="elements[selectedElement].bgColor"
                                               class="w-full h-10 rounded border border-gray-300">
                                    </div>
                                </template>

                                <template x-if="elements[selectedElement].type === 'button'">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Link</label>
                                        <input type="text" x-model="elements[selectedElement].link"
                                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                               placeholder="https://">
                                    </div>
                                </template>

                                <!-- Delete Button -->
                                <button @click="deleteElement(selectedElement)"
                                        class="w-full px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                    Remover Elemento
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bannerEditor() {
            return {
                canvasWidth: 1200,
                canvasHeight: 500,
                backgroundColor: '#ffffff',
                backgroundImage: '',
                backgroundOpacity: 100,
                elements: [],
                selectedElement: null,
                isDragging: false,
                isResizing: false,
                dragStartX: 0,
                dragStartY: 0,

                init() {
                    // Load existing design if editing
                    const existingDesign = @json($record->layers ?? []);
                    if (existingDesign && existingDesign.length > 0) {
                        this.elements = existingDesign;
                    }

                    // Setup drag handlers
                    document.addEventListener('mousemove', (e) => this.onMouseMove(e));
                    document.addEventListener('mouseup', () => this.stopDrag());
                },

                addElement(type) {
                    const element = {
                        type: type,
                        x: 100,
                        y: 100,
                        width: type === 'text' ? 200 : 150,
                        height: type === 'text' ? 50 : 100,
                        content: type === 'text' ? 'Clique para editar' : (type === 'button' ? 'Botão' : 'https://via.placeholder.com/150'),
                        fontSize: 24,
                        textColor: '#000000',
                        bgColor: '#3b82f6',
                        link: ''
                    };
                    this.elements.push(element);
                    this.selectedElement = this.elements.length - 1;
                },

                selectElement(index) {
                    this.selectedElement = index;
                },

                deleteElement(index) {
                    this.elements.splice(index, 1);
                    this.selectedElement = null;
                },

                getElementStyle(element) {
                    let style = `
                        left: ${element.x}px;
                        top: ${element.y}px;
                        width: ${element.width}px;
                        height: ${element.height}px;
                    `;

                    if (element.type === 'text') {
                        style += `
                            font-size: ${element.fontSize}px;
                            color: ${element.textColor};
                        `;
                    }

                    return style;
                },

                startDrag(event, index) {
                    this.isDragging = true;
                    this.selectedElement = index;
                    this.dragStartX = event.clientX - this.elements[index].x;
                    this.dragStartY = event.clientY - this.elements[index].y;
                },

                startResize(event, index) {
                    this.isResizing = true;
                    this.selectedElement = index;
                    this.dragStartX = event.clientX;
                    this.dragStartY = event.clientY;
                },

                onMouseMove(event) {
                    if (this.isDragging && this.selectedElement !== null) {
                        this.elements[this.selectedElement].x = event.clientX - this.dragStartX;
                        this.elements[this.selectedElement].y = event.clientY - this.dragStartY;
                    } else if (this.isResizing && this.selectedElement !== null) {
                        const deltaX = event.clientX - this.dragStartX;
                        const deltaY = event.clientY - this.dragStartY;
                        this.elements[this.selectedElement].width = Math.max(50, this.elements[this.selectedElement].width + deltaX);
                        this.elements[this.selectedElement].height = Math.max(30, this.elements[this.selectedElement].height + deltaY);
                        this.dragStartX = event.clientX;
                        this.dragStartY = event.clientY;
                    }
                },

                stopDrag() {
                    this.isDragging = false;
                    this.isResizing = false;
                },

                updateElementContent(event, index) {
                    this.elements[index].content = event.target.innerText;
                },

                updateCanvasSize() {
                    // Optionally adjust elements if needed
                },

                saveDesign() {
                    // Send to server
                    fetch('{{ route("filament.admin.resources.banners.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            layers: this.elements,
                            width: this.canvasWidth,
                            height: this.canvasHeight,
                            backgroundColor: this.backgroundColor,
                            backgroundImage: this.backgroundImage,
                            backgroundOpacity: this.backgroundOpacity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route("filament.admin.resources.banners.index") }}';
                        }
                    });
                }
            }
        }
    </script>
</x-filament-panels::page>
