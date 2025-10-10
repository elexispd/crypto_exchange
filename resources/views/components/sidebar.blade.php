<aside id="sidebar" class="sidebar">

        <ul class="sidebar-nav" id="sidebar-nav">

            <li class="nav-item">
                <a class="nav-link " href="index.html">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
            </li><!-- End Dashboard Nav -->


            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#users" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-people"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="users" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('users.create') }}">
                            <i class="bi bi-circle"></i><span>Register User</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}">
                            <i class="bi bi-circle"></i><span>View Users</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#wallet" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-cash-coin"></i><span>Wallet</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="wallet" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('admin.wallet.create') }}">
                            <i class="bi bi-circle"></i><span>Create Wallet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.wallet.index') }}">
                            <i class="bi bi-circle"></i><span>View Wallets</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#deposit" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-bank"></i><span>Deposits</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="deposit" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('admin.deposit.index', ['status' => 'pending']) }}">
                            <i class="bi bi-circle"></i><span>Pending Deposits</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.deposit.index', ['status' => 'approved']) }}">
                            <i class="bi bi-circle"></i><span>Approved Deposits</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.deposit.index', ['status' => 'rejected']) }}">
                            <i class="bi bi-circle"></i><span>Rejected Deposits</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.deposit.index') }}">
                            <i class="bi bi-circle"></i><span>All Deposits</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#transactions" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-currency-exchange"></i><span>Transactions</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="transactions" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="#">
                            <i class="bi bi-circle"></i><span>Swaps</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="bi bi-circle"></i><span>All Transactions</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" data-bs-target="#kyc" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-file-earmark-person"></i><span>KYCs</span><i class="bi bi-chevron-down ms-auto"></i>
                </a>
                <ul id="kyc" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                    <li>
                        <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}">
                            <i class="bi bi-circle"></i><span>Pending KYCs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kyc.index', ['status' => 'verified']) }}">
                            <i class="bi bi-circle"></i><span>Approved KYCs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kyc.index', ['status' => 'rejected']) }}">
                            <i class="bi bi-circle"></i><span>Rejected KYCs</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kyc.index') }}">
                            <i class="bi bi-circle"></i><span>All KYCs</span>
                        </a>
                    </li>
                </ul>
            </li>

        </ul>

    </aside><!-- End Sidebar-->
