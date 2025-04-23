<template>
  <div
    class="modal fade"
    tabindex="-1"
    aria-labelledby="editModalLabel"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">
            {{ $t('editRoadMap') }}
          </h5>
          <button
            type="button"
            class="btn-close"
            @click="closeModal"
            aria-label="Close"
          ></button>
        </div>

        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="mb-3">
              <label for="stage" class="form-label">{{ $t('stage') }}</label>
              <input
                type="number"
                id="stage"
                class="form-control"
                v-model="formData.stage"
                @input="clearFieldError('stage')"
                required
                :disabled="isLoading"
              />
              <small v-if="errors.stage" class="text-danger">{{ errors.stage[0] }}</small>
            </div>

            <div class="mb-3">
              <label for="instance" class="form-label">{{ $t('instance') }}</label>
              <input
                type="text"
                id="instance"
                class="form-control"
                v-model="formData.instance"
                @input="clearFieldError('instance')"
                required
                :disabled="isLoading"
              />
              <small v-if="errors.instance" class="text-danger">{{ errors.instance[0] }}</small>
            </div>

            <div class="mb-3">
              <label for="users" class="form-label">{{ $t('users') }}</label>
              <multiselect
                v-model="formData.users"
                :options="users"
                :multiple="true"
                :close-on-select="false"
                :clear-on-select="false"
                :preserve-search="true"
                placeholder="Select users"
                label="name"
                track-by="id"
                :disabled="isLoading"
                @input="clearFieldError('users')"
              />
              <small v-if="errors.users" class="text-danger">{{ errors.users[0] }}</small>
            </div>
          </div>

          <div class="modal-footer">
            <button
              type="submit"
              class="btn btn-primary me-1"
              :disabled="isLoading"
            >
              {{ isLoading ? $t('saving') : $t('update') }}
            </button>
            <button
              type="button"
              class="btn btn-secondary ms-1"
              @click="closeModal"
              :disabled="isLoading"
            >
              {{ $t('close') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import apiClient from '@/axios';
import * as bootstrap from 'bootstrap';
import { useFormErrors } from '@/composables/useFormErrors';
import Multiselect from '@vueform/multiselect';

export default {
  name: 'EditModal',
  components: { Multiselect },
  
  props: {
    roadMap: { type: Object, required: true },
    isVisible: { type: Boolean, required: true },
  },
  
  emits: ['save-success', 'close'],

  setup(props, { emit }) {
    const formData = ref({ id: null, stage: '', instance: '', users: [] });
    const isLoading = ref(false);
    const modalRef = ref(null);
    const { errors, clearErrors, clearFieldError } = useFormErrors();
    const users = ref([]);
    let modalInstance = null;

    const showModal = () => {
      if (modalRef.value && !modalInstance) {
        modalInstance = new bootstrap.Modal(modalRef.value, { backdrop: 'static', keyboard: false });
      }
      modalInstance?.show();
    };

    const hideModal = () => {
      modalInstance?.hide();
    };

    const closeModal = () => {
      hideModal();
      clearErrors();
      emit('close');
    };

    const fetchUsers = async () => {
      try {
        const response = await apiClient.get('/admin/user/get');
        users.value = response.data?.data ?? [];
      } catch (error) {
        console.error('Error fetching users:', error);
      }
    };

    const submitForm = async () => {
      isLoading.value = true;
      clearErrors();

      try {
        const response = await apiClient.put(`/admin/road-map/update/${formData.value.id}`, formData.value);

        if (response.data.success) {
          emit('save-success');
        } else {
          errors.value = response.data.errors || {};
        }
      }
      catch (error) {
        errors.value = error.response?.data?.errors || {};
        console.error('Errors:', error.response?.data?.errors);
      } finally {
        isLoading.value = false;
      }
    };

    watch(() => props.roadMap, (newRoadMap) => {
      if (newRoadMap) {
        formData.value = { ...newRoadMap };
      }
    }, { immediate: true });

    watch(() => props.isVisible, (visible) => {
      if (visible) {
        showModal();
      } else {
        hideModal();
      }
    });

    // Dastlabki ma'lumotlarni yuklash
    fetchUsers();

    return {
      formData,
      isLoading,
      modalRef,
      errors,
      users,
      clearFieldError,
      closeModal,
      submitForm,
    };
  },
};
</script>

<style src="@vueform/multiselect/themes/default.css"></style>
