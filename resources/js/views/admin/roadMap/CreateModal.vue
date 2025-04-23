<template>
  <div
    class="modal modal-lg fade"
    style="background: rgba(0, 0, 0, 0.1);"
    tabindex="-1"
    ref="modalRef"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createModalLabel">
            {{ $t('addRoadMap') }}
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
            <div class="row">
              <div class="col-md-3">
                <div class="mb-3">
                  <label for="stage" class="form-label">{{ $t('stage') }}</label>
                  <input
                    type="number"
                    id="stage"
                    min="1"
                    class="form-control"
                    v-model="formData.stage"
                    @input="clearFieldError('stage')"
                    required
                    :disabled="isLoading"
                  />
                  <small v-if="errors.stage" class="text-danger">{{ errors.stage[0] }}</small>
                </div>
              </div>
              <div class="col-md-9">
                <div class="mb-3">
                  <InstanceSelect
                    v-model="formData.instanceId"
                    :disabled="isLoading"
                    :error="errors.instance_id ? errors.instance_id[0] : ''"
                  />
                <small v-if="errors.instance_id" class="text-danger">{{ errors.instance_id[0] }}</small>
                </div>
              </div>
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="users" class="form-label">{{ $t('users') }}</label>
                  <multiselect
                    v-model="formData.userIds"
                    :options="users"
                    :multiple="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :preserve-search="true"
                    placeholder="Select users"
                    label="full_name"
                    track-by="id"
                    valueProp="id" 
                    :disabled="isLoading"
                    @input="clearFieldError('userIds')"
                  />
                  
                  <small v-if="errors.users" class="text-danger">{{ errors.users[0] }}</small>
                </div>
              </div>
            </div>
          </div>


          <div class="modal-footer">
            <button
              type="submit"
              class="btn btn-primary me-1"
              :disabled="isLoading"
            >
              {{ isLoading ? $t('saving') : $t('create') }}
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
import { defineAsyncComponent } from 'vue';
import apiClient from '@/axios';
import Multiselect from '@vueform/multiselect';
import { useRoadMapModal } from './useRoadMapModal';
import { useFormErrors } from '@/composables/useFormErrors';
// import '@vueform/multiselect/themes/default.css';

export default {
  name: 'CreateModal',
  components: {
    InstanceSelect: defineAsyncComponent(() => import('../component/InstanceSelect.vue')),
    Multiselect
  },
  props: {
    isVisible: { type: Boolean, required: true },
  },
  
  emits: ['save-success', 'close'],

  setup(props, { emit }) {
    const { users, modalRef, formData, isLoading, errors, closeModal } = useRoadMapModal(props, emit);
    const { clearErrors, clearFieldError } = useFormErrors();
    

    const submitForm = async () => {
      isLoading.value = true;

      try {
        const response = await apiClient.post('/admin/road-map/create', formData.value);

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

    // Har bir input uchun xatolarni tozalash
    // Object.keys(formData.value).forEach((key) => { 
    //   watch(
    //     () => formData.value[key],
    //     () => {
    //       if (errors.value[key]) delete errors.value[key];
    //     }
    //   );
    // });

    return {
      modalRef,
      formData,
      isLoading,
      errors,
      users,
      closeModal,
      submitForm,
      clearErrors,
      clearFieldError
    };
  },
};
</script>

<!-- <style src="@vueform/multiselect/themes/default.css"></style> -->
