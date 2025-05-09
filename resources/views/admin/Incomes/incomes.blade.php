@extends('layouts.app')

@section('title', 'Incomes')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="container-fluid">
        <h1 class="mb-4 text-center">Incomes</h1>
        <div class="card">
          <div class="flex-grow-1 p-3">
            <div class="d-flex justify-content-between mb-3">
                <div class="d-flex justify-content-start">
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add
                        Category</button>
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#addSubcategoryModal">Add
                        Subcategory</button>
                </div>
                <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addIncomeModal">Add
                    Income</button>
            </div>
    
            <div class="row mb-3">
                <div class="col-sm-12">
                    <input type="text" id="search-input" class="form-control border" placeholder="Search for Items...">
                </div>
            </div>
    
            <div class="table-responsive">
                <table id="sortableTable" class="table ">
                    <thead>
                        <tr>
                            <th onclick="sortTable(0, this)">Client <span class="arrow"></span></th>
                            <th onclick="sortTable(1, this)">Category <span class="arrow"></span></th>
                            <th onclick="sortTable(2, this)">Subcategory <span class="arrow"></span></th>
                            <th onclick="sortTable(3, this)">Amount <span class="arrow"></span></th>
                            <th onclick="sortTable(4, this)">Paid <span class="arrow"></span></th>
                            <th onclick="sortTable(5, this)">Status <span class="arrow"></span></th>
                            <th onclick="sortTable(6, this)">Description <span class="arrow"></span></th>
                            <th onclick="sortTable(7, this)">Date <span class="arrow"></span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="flex align-items-center">
              @foreach($incomes as $income)
                            <tr class="align-middle">
                                <td>{{ $income->client->client_fname }} {{ $income->client->client_lname }}</td>
                                <td> @if($income->subcategory && $income->subcategory->category)
                                  {{ $income->subcategory->category->category_name }}
                                  @else
                                      N/A
                                  @endif</td>
                                <td>{{ $income->subcategory->sub_name }}</td>
                                <td> ${{$income->amount}}</td>
                                <td> ${{$income->paid}}</td>
                                <td> <span class="badge bg-{{ 
                                  $income->status == 'complete' ? 'success' : 
                                  ($income->status == 'partial' ? 'warning' : 'danger') 
                              }}">
                                  {{ ucfirst($income->status) }}
                              </span></td>
                                <td>{{ Str::limit($income->description, 30) }}</td>
                                <td>{{ date('M d, Y', strtotime($income->created_at)) }}</td>
                                <td class="text-center align-middle">
                                  <div class="d-flex justify-content-center gap-2">
                                      <a id="testbtn" class='btn btn-primary' href="{{route('details',$income)}}">Details</a>
                                      <button id="delete-btn" class='btn btn-danger' data-bs-toggle='modal'
                                          data-bs-target='#deleteIncomeModal' data-income-id="{{$income->income_id}}"
                                          data-client-name="{{$income->client->client_fname}}{{$income->client->client_lname}}">
                                          <span class="d-sm-inline d-none">Delete</span>
                                          <span class="d-inline d-sm-none">D</span>
                                      </button>
                                  </div>
                              </td>
                            </tr>
      @endforeach
                    </tbody>
                </table>
            </div>
    
        </div>
        </div>
    </div>

{{-- add income model --}}
@include('admin.incomes.partials.add-income',['categories'=>$categories,'clients'=>$clients,'subcategories'=>$subcategories])
{{-- add category  model --}}
@include('admin.incomes.partials.add-category')
{{-- add sybcategory model --}}
@include('admin.incomes.partials.add-subcategory',['categories'=>$categories])
{{-- delete income model --}}
@include('admin.incomes.partials.delete-income')    

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete modal show
    const deleteModal = document.getElementById('deleteIncomeModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('delete_income_id').value = button.dataset.incomeId;
            document.getElementById('delete_income_name').textContent = button.dataset.clientName;
        });
    }

    // Handle delete form submission
    const deleteForm = document.getElementById('deleteIncomeForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const incomeId = document.getElementById('delete_income_id').value;
            
            try {
                const response = await fetch(`/admin/delete-income/${incomeId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Delete failed');
                }
                
                alert(data.message || 'Income deleted successfully');
                location.reload();
                
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Failed to delete income');
            }
        });
    }
});
</script>
@endpush
@endsection