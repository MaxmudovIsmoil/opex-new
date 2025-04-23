import { ref, watch, nextTick } from 'vue';
import apiClient from '@/axios';
import * as bootstrap from 'bootstrap';

export function useRoadMapModal(props, emit) {
  const modalRef = ref(null);
  const formData = ref(getDefaultFormData());
  const isLoading = ref(false);
  const users = ref([]);
  const errors = ref({});
  const errorMessage = ref('');
  let roadMapModal = null;

  function getDefaultFormData() {
    return {
      id: null,
      stage: null,
      instanceId: null,
      userIds: []
    };
  }

  const fetchData = async () => {
    try {
      const [usersRes] = await Promise.all([
        apiClient.get('/admin/user/get')
      ]);
      users.value = usersRes.data?.data ?? [];
      console.log('users: ', users);
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  };


  
  const showModal = () => {
    if (!modalRef.value) {
      console.error('Modal ref topilmadi!');
      return;
    }
  
    if (!roadMapModal) {
      roadMapModal = new bootstrap.Modal(modalRef.value, {
        backdrop: 'static',
        keyboard: false,
      });
    }
    roadMapModal.show();
  };
  
  

  const hideModal = () => {
    if (roadMapModal) {
      roadMapModal.hide();
      roadMapModal = null;
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
        fetchData();
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
    users,
    errors,
    errorMessage,
    closeModal,
  };
}