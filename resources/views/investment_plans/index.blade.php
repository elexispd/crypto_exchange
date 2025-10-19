@extends('layouts.portal')

@section('content')
<div class="pagetitle">
    <h1>Investment Plans</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Plans</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h6 class="card-title">Plan Listing</h6>
            <x-alerts />

            <table class="table table-striped datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Min. Amount</th>
                        <th>Interest Rate</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plans as $plan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>${{ number_format($plan->min_amount, 2) }}</td>
                        <td>{{ $plan->interest_rate }}%</td>
                        <td>
                            <span class="badge bg-{{ $plan->status ? 'success' : 'secondary' }}">
                                {{ $plan->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <button
                                class="btn btn-sm btn-primary editPlanBtn"
                                data-id="{{ $plan->id }}"
                                data-name="{{ $plan->name }}"
                                data-min="{{ $plan->min_amount }}"
                                data-interest="{{ $plan->interest_rate }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-warning changeStatusBtn"
                                data-id="{{ $plan->id }}">
                                Toggle Status
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.plan.update') }}">
            @csrf
            <input type="hidden" name="plan_id" id="plan_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit Plan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Plan Name</label>
                        <input type="text" name="name" id="plan_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Min Amount</label>
                        <input type="number" step="any" name="min_amount" id="plan_min" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Interest Rate (%)</label>
                        <input type="number" step="any" name="interest_rate" id="plan_interest" class="form-control"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Handle Edit button click
    document.querySelectorAll('.editPlanBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('plan_id').value = this.dataset.id;
            document.getElementById('plan_name').value = this.dataset.name;
            document.getElementById('plan_min').value = this.dataset.min;
            document.getElementById('plan_interest').value = this.dataset.interest;
        });
        console.log(32)
    });

    // Handle Change Status button
    document.querySelectorAll('.changeStatusBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;

            fetch("{{ route('admin.plan.changeStatus') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ plan_id: id })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert("Failed to change status");
                }
            });
        });
    });
});
</script>
@endsection
