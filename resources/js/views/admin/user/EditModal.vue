<template>
  <div
    class="modal fade modal-lg"
    tabindex="-1"
    aria-labelledby="editUserModalLabel"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">
            {{ $t('editUser') }}
          </h5>
          <button type="button" class="btn-close" @click="closeModal"></button>
        </div>

        <form @submit.prevent="submitForm">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <RoadSelect v-model="formData.order_type_id" :roads="roads" :disabled="isLoading" />
                <small v-if="errors.order_type_id" class="text-danger">{{ errors.order_type_id[0] }}</small>
              </div>
              <div class="col-md-6 mb-3">
                <InstanceSelect
                  v-model="formData.instance_id"
                  :instances="instances"
                  :disabled="isLoading"
                  :error="errors.instance_id ? errors.instance_id[0] : ''"
                />
                <small v-if="errors.instance_id" class="text-danger">{{ errors.instance_id[0] }}</small>
              </div>

              <div class="col-md-6 mb-3">
                <label for="full_name" class="form-label">{{ $t('fullName') }}</label>
                <input
                  type="text"
                  id="full_name"
                  class="form-control"
                  v-model="formData.full_name"
                  required
                  :disabled="isLoading"
                />
                <small v-if="errors.full_name" class="text-danger">{{ errors.full_name[0] }}</small>
              </div>

              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">{{ $t('email') }}</label>
                <input
                  type="email"
                  id="email"
                  class="form-control"
                  v-model="formData.email"
                  required
                  :disabled="isLoading"
                />
                <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
              </div>

              <div class="col-md-6 mb-3">
                <label for="username" class="form-label">{{ $t('username') }}</label>
                <input
                  type="text"
                  id="username"
                  class="form-control"
                  v-model="formData.username"
                  required
                  :disabled="isLoading"
                />
                <small v-if="errors.username" class="text-danger">{{ errors.username[0] }}</small>
              </div>

              <div class="col-md-3 mb-3">
                <label for="status" class="form-label">{{ $t('status') }}</label>
                <select id="status" class="form-control" v-model="formData.status" :disabled="isLoading">
                  <option :value="1">{{ $t('active') }}</option>
                  <option :value="0">{{ $t('inactive') }}</option>
                </select>
              </div>

              <div class="col-md-3 mb-3">
                <label for="language" class="form-label">{{ $t('language') }}</label>
                <select id="language" class="form-control" v-model="formData.language" :disabled="isLoading">
                  <option value="en">{{ $t('english') }}</option>
                  <option value="ru">{{ $t('russian') }}</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="can_create_order" class="form-label">{{ $t('canCreateOrder') }}</label>
                <select
                  id="can_create_order"
                  class="form-control"
                  v-model.number="formData.can_create_order"
                  :disabled="isLoading"
                >
                  <option :value="1">{{ $t('yes') }}</option>
                  <option :value="0">{{ $t('no') }}</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="can_order_detail_edit" class="form-label">{{ $t('canOrderDetailEdit') }}</label>
                <select
                  id="can_order_detail_edit"
                  class="form-control"
                  v-model.number="formData.can_order_detail_edit"
                  :disabled="isLoading"
                >
                  <option :value="1">{{ $t('yes') }}</option>
                  <option :value="0">{{ $t('no') }}</option>
                </select>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary me-1" :disabled="isLoading">
              {{ isLoading ? $t('saving') : $t('update') }}
            </button>
            <button type="button" class="btn btn-secondary ms-1" @click="closeModal" :disabled="isLoading">
              {{ $t('close') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent, watch } from 'vue';
import apiClient from '@/axios';
import { useUserModal } from './useUserModal';

export default {
  name: 'EditUserModal',
  components: {
    RoadSelect: defineAsyncComponent(() => import('../component/RoadSelect.vue')),
    InstanceSelect: defineAsyncComponent(() => import('../component/InstanceSelect.vue')),
  },
  props: {
    user: { 
      type: Object,
      default: () => ({
        id: null,
        order_type_id: null,
        instance_id: null,
        full_name: '',
        email: '',
        username: '',
        status: 1,
        can_create_order: 0,
        can_order_detail_edit: 0,
        language: 'en'
      })
    },
    isVisible: { type: Boolean, required: true },
  },
  emits: ['save-success', 'close'],

  setup(props, { emit }) {
    const { modalRef, formData, isLoading, roads, instances, errors, errorMessage, closeModal } = useUserModal(
      props,
      emit
    );

    const submitForm = async () => {
      isLoading.value = true;
      errorMessage.value = '';
      errors.value = {};

      try {
        const payload = { ...formData.value };
        const response = await apiClient.put(`/admin/user/update/${payload.id}`, payload);

        if (response.data.success) {
          emit('save-success');
          closeModal();
        } else {
          errors.value = response.data.errors || {};
          errorMessage.value = response.data.message || 'Operation failed!';
        }
      } catch (error) {
        errors.value = error.response?.data?.errors || {};
        errorMessage.value = error.response?.data?.message || 'Operation failed!';
      } finally {
        isLoading.value = false;
      }
    };

    watch(() => props.user, (newUser) => {
        formData.value = newUser ? { ...newUser } : {};
      },
      { immediate: true, deep: true }
    );

    // Har bir input uchun xatolarni tozalash
    Object.keys(formData.value).forEach((key) => {watch(() => formData.value[key], () => {
          if (errors.value[key]) delete errors.value[key];
        }
      );
    });

    return {
      modalRef,
      formData,
      isLoading,
      roads,
      instances,
      errors,
      errorMessage,
      submitForm,
      closeModal,
    };
  },
};
</script>