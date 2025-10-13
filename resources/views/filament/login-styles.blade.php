@inject('config', 'App\Models\BlogConfig')

<style>
:root {
    --login-bg-image: url('{{ $config->getInstance()->login_image_url }}');
}
</style>