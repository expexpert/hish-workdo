
{{ Form::model($category, ['route' => ['customer-category.update', $category->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('name', __('Category Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder'=>__('Enter Category Name')]) }}
        </div>
        <div class="form-group col-md-12 d-block">
            {{ Form::label('description', __('Category Description'),['class'=>'form-label']) }}<x-required></x-required>
            {{ Form::textarea('description', null, ['class' => 'form-control', 'required' => 'required', 'placeholder'=>__('Enter Category Description'), 'rows' => 3]) }}
        </div>
        <div class="form-group col-md-12 account">
            {{Form::label('is_active',__('Status'),['class'=>'form-label'])}}
            <select class="form-control select" name="is_active" id="is_active">
                <option value="1" {{ $category->is_active == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="0" {{ $category->is_active == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}