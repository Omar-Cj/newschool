@foreach ($students as $item)
<tr id="document-file">
    <td>{{ @$item->student->admission_no }}</td>
    <td>{{ @$item->student->first_name }} {{ @$item->student->last_name }}</td>
    <td>{{ @$item->class->name }}</td>
    <td>{{ @$item->section->name }}</td>
    <td>{{ @$item->student->parent->guardian_name }}</td>
    <td>{{ @$item->student->mobile }}</td>
    <td>
        <button type="button" class="btn btn-sm ot-btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#modalCustomizeWidth"
                onclick="openFeeCollectionModal({{ $item->student->id }}, '{{ $item->student->first_name }} {{ $item->student->last_name }}')">
            <i class="fas fa-credit-card me-1"></i>
            {{ ___('common.Collect')}}
        </button>
    </td>
</tr>
@endforeach

