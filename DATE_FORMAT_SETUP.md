# Date Format Configuration

## Overview
The application uses **dd-mm-yyyy** (e.g., 29-12-2025) as the default date format throughout the entire application. This format is consistent across all systems, regardless of the computer or laptop being used.

## Configuration

The date format is configured in `config/app.php`:
```php
'date_format' => 'd-m-Y',
```

## Usage

### 1. In Blade Templates

#### Using the `@date` directive:
```blade
{{-- Display date only --}}
@date($debitNote->debit_note_date)
{{-- Output: 29-12-2025 --}}

{{-- Display date with time --}}
@datetime($debitNote->created_at)
{{-- Output: 29-12-2025 14:30:45 --}}
```

#### Using the helper function:
```blade
{{ formatDate($debitNote->debit_note_date) }}
{{ formatDateTime($debitNote->created_at) }}
```

#### Using Carbon's macro:
```blade
{{ $debitNote->debit_note_date->toDisplayDate() }}
{{ $debitNote->created_at->toDisplayDateTime() }}
```

### 2. In Controllers

```php
use function App\Helpers\formatDate;
use function App\Helpers\formatDateTime;

// Format date
$formattedDate = formatDate($debitNote->debit_note_date);

// Format date with time
$formattedDateTime = formatDateTime($debitNote->created_at);

// Using Carbon macro
$formattedDate = $debitNote->debit_note_date->toDisplayDate();
```

### 3. In JavaScript/Form Inputs

For date input fields, use `Y-m-d` format (HTML5 date input requirement):
```blade
<input type="date" name="debit_note_date" 
       value="{{ old('debit_note_date', $editing ? optional($debitNote->debit_note_date)->format('Y-m-d') : now()->format('Y-m-d')) }}">
```

But when displaying dates to users, always use `d-m-Y`:
```blade
{{ formatDate($debitNote->debit_note_date) }}
```

## Important Notes

1. **Database Storage**: Dates are stored in the database using MySQL's standard format (YYYY-MM-DD). This is handled automatically by Laravel.

2. **Form Inputs**: HTML5 date inputs require `Y-m-d` format, but this is only for the input value. Display dates to users using `d-m-Y`.

3. **Consistency**: Always use the helper functions or Blade directives to ensure consistent date formatting across the entire application.

4. **System Independence**: The date format is configured in the application code, not dependent on system locale settings, ensuring consistency across different computers.

## Examples

### Before (Inconsistent):
```blade
{{ $debitNote->debit_note_date->format('Y-m-d') }}  {{-- Wrong format --}}
{{ $debitNote->debit_note_date->format('d M Y') }}  {{-- Wrong format --}}
{{ $debitNote->debit_note_date->format('m/d/Y') }}  {{-- Wrong format --}}
```

### After (Consistent):
```blade
@date($debitNote->debit_note_date)  {{-- Correct: 29-12-2025 --}}
{{ formatDate($debitNote->debit_note_date) }}  {{-- Correct: 29-12-2025 --}}
{{ $debitNote->debit_note_date->toDisplayDate() }}  {{-- Correct: 29-12-2025 --}}
```

## Migration Guide

To update existing code to use the new date format:

1. Replace `->format('Y-m-d')` with `formatDate()` or `@date()` directive
2. Replace `->format('d-m-Y')` with `formatDate()` or `@date()` directive (already correct format, but use helper for consistency)
3. Replace `->format('d M Y')` with `formatDate()` or `@date()` directive
4. Replace `->format('m/d/Y')` with `formatDate()` or `@date()` directive

## Configuration Location

- **Config File**: `config/app.php` - `date_format` setting
- **Service Provider**: `app/Providers/AppServiceProvider.php` - Carbon macros and Blade directives
- **Helper Functions**: `app/Helpers/functions.php` - `formatDate()` and `formatDateTime()` functions

