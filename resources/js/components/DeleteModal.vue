<template>
  <div class="modal fade" id="deleteModal"  tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {{ $t('confirm_delete') }}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('cancel') }}</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">{{ $t('delete') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>
