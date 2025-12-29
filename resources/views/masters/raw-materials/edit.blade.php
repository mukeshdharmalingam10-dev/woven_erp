@extends('layouts.dashboard')

@section('title', 'Edit Raw Material - Woven_ERP')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Raw Material</h2>
        <a href="{{ route('raw-materials.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('raw-materials.update', $rawMaterial->id) }}" method="POST" id="rawMaterialForm">
        @csrf
        @method('PUT')

        <div style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 18px; margin-bottom: 15px;">Basic Information</h3>

            <div style="margin-bottom: 20px;">
                <label for="raw_material_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Raw Material Name <span style="color: red;">*</span></label>
                <input type="text" name="raw_material_name" id="raw_material_name" value="{{ old('raw_material_name', $rawMaterial->raw_material_name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Enter raw material name">
                @error('raw_material_name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="unit_of_measure" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Unit of Measure <span style="color: red;">*</span></label>
                <select name="unit_of_measure" id="unit_of_measure" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                    <option value="">Select unit</option>
                    <option value="KG" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'KG' ? 'selected' : '' }}>KG (Kilogram)</option>
                    <option value="G" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'G' ? 'selected' : '' }}>G (Gram)</option>
                    <option value="MT" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'MT' ? 'selected' : '' }}>MT (Metric Ton)</option>
                    <option value="Nos" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'Nos' ? 'selected' : '' }}>Nos (Numbers)</option>
                    <option value="L" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'L' ? 'selected' : '' }}>L (Liters)</option>
                    <option value="ML" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'ML' ? 'selected' : '' }}>ML (Milliliters)</option>
                    <option value="M" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'M' ? 'selected' : '' }}>M (Meters)</option>
                    <option value="CM" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'CM' ? 'selected' : '' }}>CM (Centimeters)</option>
                    <option value="FT" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'FT' ? 'selected' : '' }}>FT (Feet)</option>
                    <option value="IN" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'IN' ? 'selected' : '' }}>IN (Inches)</option>
                    <option value="PCS" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'PCS' ? 'selected' : '' }}>PCS (Pieces)</option>
                    <option value="BOX" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'BOX' ? 'selected' : '' }}>BOX (Boxes)</option>
                    <option value="PKT" {{ old('unit_of_measure', $rawMaterial->unit_of_measure) === 'PKT' ? 'selected' : '' }}>PKT (Packets)</option>
                </select>
                @error('unit_of_measure')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
                <textarea name="description" id="description" rows="3"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                    placeholder="Enter description or additional details about the raw material">{{ old('description', $rawMaterial->description) }}</textarea>
                @error('description')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Reorder Level field hidden - preserve existing value --}}
            <input type="hidden" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', $rawMaterial->reorder_level ?? 0) }}">
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="resetForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Reset
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('raw-materials.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function resetForm() {
        document.getElementById('rawMaterialForm').reset();
        // Restore original values
        document.getElementById('raw_material_name').value = '{{ addslashes($rawMaterial->raw_material_name) }}';
        document.getElementById('unit_of_measure').value = '{{ addslashes($rawMaterial->unit_of_measure) }}';
        document.getElementById('reorder_level').value = '{{ (int)$rawMaterial->reorder_level }}';
    }
</script>
@endpush
@endsection

