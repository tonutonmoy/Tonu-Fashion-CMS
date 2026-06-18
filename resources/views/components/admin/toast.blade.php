<div id="admin-toast-root" class="fixed top-4 right-4 z-[100] space-y-2 pointer-events-none"></div>

@if(session('success'))
    <div data-admin-toast data-type="success" class="hidden">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div data-admin-toast data-type="error" class="hidden">{{ session('error') }}</div>
@endif
@if(session('status'))
    <div data-admin-toast data-type="info" class="hidden">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div data-admin-toast data-type="error" class="hidden">{{ $errors->first() }}</div>
@endif
