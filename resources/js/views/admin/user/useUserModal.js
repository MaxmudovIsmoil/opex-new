import { ref, watch, nextTick } from 'vue';
import * as bootstrap from 'bootstrap';

export function useUserModal(props, emit) {
  const modalRef = ref(null);
  const formData = ref(getDefaultFormData());
  const isLoading = ref(false);
  const errors = ref({});
  const errorMessage = ref('');
  let modalUser = null;

  function getDefaultFormData() {
    return {
      id: null,
      order_type_id: null,
      instance_id: null,
      full_name: '',
      email: '',
      username: '',
      status: 1,
      can_create_order: 0,
      can_order_detail_edit: 0,
      language: 'en',
    };
  }

  
  const showModal = () => {
    if (!modalRef.value) return;
    if (!modalUser) {
      modalUser = new bootstrap.Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false,
      });
    }
    modalUser.show();
  };

  const hideModal = () => {
    if (modalUser) {
      modalUser.hide();
      modalUser = null;
    }
  };

  const closeModal = () => {
    hideModal();
    emit('close');
    formData.value = getDefaultFormData();
    errors.value = {};
    errorMessage.value = '';
  };

  watch(() => props.isVisible, (newValue) => {
      if (newValue) {
        nextTick(() => showModal());
      } 
      else {
        hideModal();
      }
    }
  );

  return {
    modalRef,
    formData,
    isLoading,
    errors,
    errorMessage,
    getDefaultFormData,
    closeModal,
  };
}