@props(['name' => 'content', 'value' => '', 'label' => 'Content', 'height' => '320px'])

<div class="rich-editor" data-rich-editor data-target="{{ $name }}">
    <label class="label">{{ $label }}</label>
    <div class="rich-editor-toolbar flex flex-wrap gap-1 mb-2 p-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
        <button type="button" data-cmd="bold" class="px-2 py-1 rounded hover:bg-gray-200 font-bold" title="Bold">B</button>
        <button type="button" data-cmd="italic" class="px-2 py-1 rounded hover:bg-gray-200 italic" title="Italic">I</button>
        <button type="button" data-cmd="underline" class="px-2 py-1 rounded hover:bg-gray-200 underline" title="Underline">U</button>
        <button type="button" data-cmd="insertUnorderedList" class="px-2 py-1 rounded hover:bg-gray-200" title="Bullet list">• List</button>
        <button type="button" data-cmd="insertOrderedList" class="px-2 py-1 rounded hover:bg-gray-200" title="Numbered list">1. List</button>
        <button type="button" data-cmd="createLink" class="px-2 py-1 rounded hover:bg-gray-200" title="Link">Link</button>
        <button type="button" data-cmd="formatBlock" data-value="h2" class="px-2 py-1 rounded hover:bg-gray-200" title="Heading">H2</button>
        <button type="button" data-cmd="removeFormat" class="px-2 py-1 rounded hover:bg-gray-200" title="Clear">Clear</button>
    </div>
    <div class="rich-editor-body border border-gray-200 rounded-lg p-4 bg-white prose max-w-none overflow-y-auto focus:outline-none focus:ring-2 focus:ring-brand-500"
         contenteditable="true"
         style="min-height: {{ $height }}"
         data-editor-body>{!! old($name, $value) !!}</div>
    <textarea name="{{ $name }}" class="sr-only" data-editor-input>{{ old($name, $value) }}</textarea>
</div>
