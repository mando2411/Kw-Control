<style>
    .sorting-modal .modal-content {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 24px 55px rgba(16, 42, 67, 0.3);
    }

    .sorting-modal .modal-header {
        border-bottom: 0;
        padding: 1rem 1rem 0.8rem;
    }

    .sorting-modal .modal-header .btn-close {
        margin: 0;
    }

    .sorting-modal .modal-title {
        font-size: 1rem;
        font-weight: 800;
    }

    .sorting-modal .modal-body {
        padding: 0.65rem 1rem 1rem;
    }

    .sorting-modal .modal-icon {
        width: 66px;
        height: 66px;
        margin: 0 auto 0.8rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        background: rgba(245, 158, 11, 0.14);
        color: #b7791f;
    }

    .sorting-modal .vote-input {
        border-radius: 12px;
        border: 1px solid #bfd4e1;
        height: 44px;
        font-size: 1rem;
        font-weight: 700;
        text-align: center;
    }

    .sorting-modal .vote-input:focus {
        border-color: rgba(15, 118, 110, 0.68);
        box-shadow: 0 0 0 0.22rem rgba(15, 118, 110, 0.18);
        outline: none;
    }

    .sorting-modal .modal-footer {
        border-top: 0;
        padding: 0 1rem 1rem;
    }

    .sorting-modal .btn {
        border-radius: 11px;
        min-width: 120px;
        font-weight: 700;
    }

    .sorting-success .modal-icon {
        background: rgba(31, 157, 102, 0.16);
        color: #1f9d66;
    }
</style>

<div class="modal fade sorting-modal" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="confirmModalLabel">تأكيد العملية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon">
                    <i class="fas fa-question"></i>
                </div>
                <h6 id="confirmMessage" class="mb-3 fw-bold text-dark"></h6>
                <input type="number" id="vote_count" name="vote_count" class="form-control vote-input" min="0" placeholder="أدخل عدد الأصوات" required>
                <input type="hidden" id="candidateIdInput">
                <input type="hidden" id="statusInput">
                <input type="hidden" id="confirmCommitteeInput">
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times ms-1"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" id="confirmButton">
                    <i class="fas fa-check ms-1"></i>تأكيد
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade sorting-modal sorting-success" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">حالة العملية</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body text-center">
                <div class="modal-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h6 id="successMessage" class="mb-0 fw-bold"></h6>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">موافق</button>
            </div>
        </div>
    </div>
</div>