<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="label" for="title">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $expense?->title) }}" class="input" required>
        @error('title')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="label" for="category">Category</label>
        <select name="category" id="category" class="input" required>
            @foreach($categories as $category)
            <option value="{{ $category->value }}" @selected(old('category', $expense?->category?->value) === $category->value)>{{ $category->label() }}</option>
            @endforeach
        </select>
        @error('category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="label" for="amount">Amount (BDT)</label>
            <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount', $expense?->amount) }}" class="input" required>
            @error('amount')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label" for="expense_date">Date</label>
            <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense?->expense_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="input" required>
            @error('expense_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="label" for="note">Note (optional)</label>
        <textarea name="note" id="note" rows="3" class="input">{{ old('note', $expense?->note) }}</textarea>
        @error('note')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end gap-2 pt-2">
        @if(!empty($cancelUrl))
        <a href="{{ $cancelUrl }}" class="btn-secondary">Cancel</a>
        @endif
        <button type="submit" class="btn-primary">{{ ($method ?? 'POST') === 'PUT' ? 'Update' : 'Save' }} Expense</button>
    </div>
</form>
