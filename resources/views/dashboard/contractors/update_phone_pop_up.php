        <!-- ================================================================================================================= -->
        <!-- Add this HTML code right after your table section -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal" aria-label="Close"> X </button>
                        <h5 class="modal-title" id="confirmModalLabel" style="color:black">  تعديل الهاتف </h5>
                    </div>
                    <div class="modal-body text-center py-4" style="direction: rtl;">

                        <h5 id="confirmMessage" class="mb-3"></h5>
                        <input type="hidden" id="updated_voter_id" name="updated_voter_id" value="">
                        <input type="text" id="voter_phone" name="voter_phone" class="form-control"  value="" min="0" ><br>
                        
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="button" class="btn btn-success px-4" id="confirmButton">
                            <i class="fas fa-check"></i> تعديل
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================================================================================================================= -->
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">نجاح العملية</h5>

                        <button type="button" class="btn-secondary btn-close-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h5 id="successMessage"></h5>
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">موافق</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- ================================================================================================================= -->