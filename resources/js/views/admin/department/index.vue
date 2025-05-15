<template>
    <div class="container-fluid">
      <CreateOrEditModal
        :department="selectedDepartment"
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
              <th>{{ $t('department') }} ({{ $t('english') }})</th>
              <th>{{ $t('department') }} ({{ $t('russian') }})</th>
              <th width="5%" class="text-center">{{ $t('actions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="4" class="text-center">Loading...</td>
            </tr>
            <tr v-else-if="error">
              <td colspan="4" class="text-center text-danger">{{ error }}</td>
            </tr>
            <tr v-else-if="departments.length === 0">
              <td colspan="4" class="text-center">No data available</td>
            </tr>
            <tr v-for="(department, index) in departments" :key="department.id" v-else>
              <td class="text-center">{{ index + 1 }}</td>
              <td class="text-center">{{ department.id }}</td>
              <td>{{ department.name_en }}</td>
              <td>{{ department.name_ru }}</td>
              <td class="text-center">
                <button
                  class="btn btn-outline-primary btn-sm"
                  @click="openEditModal(department)"
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
    name: 'Department',
    components: { CreateOrEditModal, Search },
  
    setup() {
      const departments = ref([]);
      const isLoading = ref(false);
      const error = ref(null);
      const selectedDepartment = ref(null);
      const isModalVisible = ref(false);
  
      // Ma'lumotlarni yuklash
      const fetchDepartments = async () => {
        isLoading.value = true;
        error.value = null;
  
        try {
          const response = await apiClient.get('/admin/department/all');
          departments.value = response.data?.data ?? [];
        } catch (err) {
          error.value = err.response?.data?.message ?? 'Failed to load departments';
          console.error('Fetch error:', err);
        } finally {
          isLoading.value = false;
        }
      };
  
      // Modalni ochish
      const openModal = (department = null) => {
        selectedDepartment.value = department ? { ...department } : null;
        isModalVisible.value = true;
      };
  
      const openCreateModal = () => openModal();
      const openEditModal = (department) => openModal(department);
  
      // Modalni yopish
      const closeModal = () => {
        isModalVisible.value = false;
        selectedDepartment.value = null;
      };
  
      // Saqlash muvaffaqiyatli bo'lsa
      const handleSaveSuccess = () => {
        closeModal();  // Avval modalni yopamiz
        fetchDepartments();  // Keyin listni yangilaymiz
      };
  
      onMounted(fetchDepartments);
  
      return {
        departments,
        isLoading,
        error,
        selectedDepartment,
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