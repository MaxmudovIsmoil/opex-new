<template>
<div class="modal fade bg-dark bg-opacity-50" id="confirmModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="confirmModalLabel"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="acceptLabel">{{ $t('instance_reply') }}</h5>
            </div>
            <form action="" method="POST" class="js_order_status_form">
                <input type="hidden" name="order_id" value="4">
                <input type="hidden" name="status" class="js_status" value="1">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{ $t('reply') }}:</label>
                        <b :class="['js_text', messageColor]">{{ translatedMessage }}</b>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">{{ $t('comment') }}:</label>
                        <textarea class="form-control js_comment" name="comment" id="message-text"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">{{ $t('apply') }}</button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ $t('close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
export default {
  props: {
    message: {
      type: String,
    },
    messageColor:{
        type:String,
        default:'text-success'
    },
},
computed: {
    translatedMessage() {
        if(this.message!=null){
            
           return this.$t(this.message)
        }
        else {
            return this.message
        }
    }
  }
};
</script>
  
  
  