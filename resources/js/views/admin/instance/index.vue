<template>
  <div class="container-fluid">
    <CreateOrEditModal
      :instance="selectedInstance"
      :is-visible="isModalVisible"
      @save-success="handleSaveSuccess"
      @close="closeModal"
    />

    <div class="bg-shadow">
      <div class="row mb-2 align-items-center">
        <div class="col-auto">
          <button class="btn btn-outline-primary btn-sm" @click="openCreateModal">
            <font-awesome-icon :icon="['fas', 'plus']" /> {{ $t('add') }}
          </button>
        </div>
        <div class="col">
          <Search />
        </div>
      </div>

      <table class="table table-sm table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th class="text-center" width="3%">№</th>
            <th class="text-center" width="3%">{{ $t('id') }}</th>
            <th>{{ $t('instance') }} ({{ $t('english') }})</th>
            <th>{{ $t('instance') }} ({{ $t('russian') }})</th>
            <th width="7%" class="text-center">{{ $t('working_hours') }}</th>
            <th width="7%" class="text-center">{{ $t('actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="isLoading">
            <td colspan="6" class="text-center">Loading...</td>
          </tr>
          <tr v-else-if="error">
            <td colspan="6" class="text-center text-danger">{{ error }}</td>
          </tr>
          <tr v-else-if="instances.length === 0">
            <td colspan="6" class="text-center">No data available</td>
          </tr>
          <tr v-for="(instance, index) in instances" :key="instance.id" v-else>
            <td class="text-center">{{ index + 1 }}</td>
            <td class="text-center">{{ instance.id }}</td>
            <td>{{ instance.name_en }}</td>
            <td>{{ instance.name_ru }}</td>
            <td class="text-center">{{ instance.time_line }}</td>
            <td class="text-center">
              <button
                class="btn btn-outline-primary btn-sm"
                @click="openEditModal(instance)"
                title="Edit"
              >
                <font-awesome-icon :icon="['fas', 'pen']" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';
import CreateOrEditModal from './CreateOrEditModal.vue';
import Search from '../../../components/Search.vue';

export default {
  name: 'Instance',
  components: { CreateOrEditModal, Search },

  setup() {
    const instances = ref([]);
    const isLoading = ref(false);
    const error = ref(null);
    const selectedInstance = ref(null);
    const isModalVisible = ref(false);

    // Ma'lumotlarni yuklash
    const fetchInstancs = async () => {
      isLoading.value = true;
      error.value = null;

      try {
        const response = await apiClient.get('/admin/instance/all');
        instances.value = response.data?.data ?? [];
      } catch (err) {
        error.value = err.response?.data?.message ?? 'Failed to load instances';
        console.error('Fetch error:', err);
      } finally {
        isLoading.value = false;
      }
    };

    // Modalni ochish
    const openModal = (instance = null) => {
      selectedInstance.value = instance ? { ...instance } : null;
      isModalVisible.value = true;
    };

    const openCreateModal = () => openModal();
    const openEditModal = (instance) => openModal(instance);

    // Modalni yopish
    const closeModal = () => {
      isModalVisible.value = false;
      selectedInstance.value = null;
    };

    // Saqlash muvaffaqiyatli bo'lsa
    const handleSaveSuccess = () => {
      fetchInstancs();
      closeModal();
    };

    onMounted(fetchInstancs);

    return {
      instances,
      isLoading,
      error,
      selectedInstance,
      isModalVisible,
      openCreateModal,
      openEditModal,
      handleSaveSuccess,
      closeModal,
    };
  },
};
</script>

<style scoped>
.text-danger {
  color: #dc3545;
}
</style>