<template>
  <div
    class="modal fade"
    tabindex="-1"
    aria-labelledby="customModalLabel"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="customModalLabel">
            {{ isEditMode ? $t('editInstance') : $t('addInstance') }}
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
              <label for="name_en" class="form-label">
                {{ $t('instance') }} ({{ $t('english') }})
              </label>
              <input
                type="text"
                id="name_en"
                class="form-control"
                v-model="formData.name_en"
                @input="clearFieldError('name_en')"
                required
                :disabled="isLoading"
              />
              <small v-if="errors.name_en" class="text-danger">{{ errors.name_en[0] }}</small>
            </div>
            <div class="mb-3">
              <label for="name_ru" class="form-label">
                {{ $t('instance') }} ({{ $t('russian') }})
              </label>
              <input
                type="text"
                id="name_ru"
                class="form-control"
                v-model="formData.name_ru"
                @input="clearFieldError('name_ru')"
                required
                :disabled="isLoading"
              />
              <small v-if="errors.name_ru" class="text-danger">{{ errors.name_ru[0] }}</small>
            </div>

            <div class="mb-3">
              <label for="time_line" class="form-label">
                {{ $t('working_hours') }}
              </label>
              <input
                type="text"
                id="time_line"
                class="form-control"
                v-model="formData.time_line"
                @input="clearFieldError('time_line')"
                required
                :disabled="isLoading"
              />
              <small v-if="errors.time_line" class="text-danger">{{ errors.time_line[0] }}</small>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="submit"
              class="btn btn-primary me-1"
              :disabled="isLoading"
            >
              {{ isLoading ? $t('saving') : isEditMode ? $t('update') : $t('create') }}
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
import { ref, computed, watch, nextTick } from 'vue';
import apiClient from '@/axios';
import * as bootstrap from 'bootstrap';
import { useFormErrors } from '@/composables/useFormErrors';

export default {
  name: 'CreateOrEditModal',
  props: {
    instance: { type: Object, default: null },
    isVisible: { type: Boolean, required: true },
  },
  emits: ['save-success', 'close'],

  setup(props, { emit }) {
    const formData = ref({ id: null, name_en: '', name_ru: '', time_line: 6 });
    const isLoading = ref(false);
    const modalRef = ref(null);
    const { errors, clearErrors, clearFieldError } = useFormErrors();
    let modalInstance = null;

    const isEditMode = computed(() => !!props.instance?.id);

    const showModal = () => {
      if (modalRef.value && !modalInstance) {
        modalInstance = new bootstrap.Modal(modalRef.value, {
          backdrop: 'static',
          keyboard: false,
        });
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

    const submitForm = async () => {
      isLoading.value = true;
      try {
        const payload = { ...formData.value };
        const response = isEditMode.value
          ? await apiClient.put(`/admin/instance/update/${payload.id}`, payload)
          : await apiClient.post('/admin/instance/create', payload);

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

    watch(() => props.instance, (newInstance) => {
      formData.value = newInstance ? { ...newInstance } : { id: null, name_en: '', name_ru: '', time_line: 6 };
    }, { immediate: true });

    watch(() => props.isVisible, (newValue) => {
      nextTick(() => {
        newValue ? showModal() : hideModal();
      });
    });

    return {
      formData,
      isLoading,
      isEditMode,
      submitForm,
      closeModal,
      modalRef,
      errors,
      clearErrors, 
      clearFieldError
    };
  },
};
</script>