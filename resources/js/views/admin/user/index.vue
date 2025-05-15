<template>
  <div class="container-fluid">
    <div class="bg-shadow m-2">
      <div class="row mb-2">
        <div class="col-auto">
          <button class="btn btn-outline-primary btn-sm" @click="openCreateModal">
            <font-awesome-icon :icon="['fas', 'plus']" /> {{ $t('add') }}
          </button>
        </div>
        <div class="col">
          <Search />
        </div>
      </div>

      <table class="table table-bordered table-striped table-sm table-hover" id="user_datatable">
        <thead>
          <tr>
            <th class="text-center" width="3%">№</th>
            <th class="text-center" width="3%">{{ $t('id') }}</th> 
            <th width="15%">{{ $t('department') }}</th>
            <th width="15%">{{ $t('instance') }} / {{ $t('division') }}</th>
            <th>{{ $t('full_name') }}</th>
            <th>{{ $t('email') }}</th>
            <th>{{ $t('username') }}</th>
            <th width="5%" class="text-center">{{ $t('status') }}</th>
            <th width="5%" class="text-center">{{ $t('language') }}</th>
            <th width="6%" class="text-end">{{ $t('actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="isLoading">
            <td colspan="10" class="text-center">Loading...</td>
          </tr>
          <tr v-else-if="error">
            <td colspan="10" class="text-center">{{ error }}</td>
          </tr>
          <tr v-else-if="!users.length">
            <td colspan="10" class="text-center">No users found</td>
          </tr>
          <tr v-for="(user, index) in users" :key="user.id">
            <td class="text-center">{{ startIndex + index + 1 }}</td>
            <td class="text-center">{{ user.id }}</td>
            <td>{{ user.roadMap || '-' }}</td>
            <td>{{ user.instanceName }}</td>
            <td>{{ user.full_name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.username }}</td>
            <td class="text-center">
              <font-awesome-icon v-if="user.status == 1" class="text-success" :icon="['fas', 'check']" />
              <font-awesome-icon v-else class="text-danger" :icon="['fas', 'times']" />
            </td>
            <td class="text-center">{{ user.language }}</td>
            <td class="text-end">
              <div>
                <a class="btn btn-outline-primary btn-sm me-1"
                  @click="openEditModal(user)"
                  title="Edit">
                  <font-awesome-icon :icon="['fas', 'pen']" />
                </a>
                <a class="btn btn-outline-danger btn-sm ms-1" title="Delete">
                  <font-awesome-icon :icon="['fas', 'trash-alt']" />
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    
      <Pagination
        v-if="totalItems > 0"
        :current-page="currentPage"
        :total-items="totalItems"
        :items-per-page="itemsPerPage"
        @change-page="handlePageChange"
      />
    </div>

    <CreateModal
      :is-visible="isCreateModalVisible"
      @save-success="handleSaveSuccess"
      @close="closeCreateModal"
    />
    
    <EditModal
      :user="selectedUser"
      :is-visible="isEditModalVisible"
      @save-success="handleSaveSuccess"
      @close="closeEditModal"
    />
    
    <DeleteModal />
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import apiClient from '@/axios';
import Pagination from '../../../components/Pagination.vue';
import DeleteModal from '../../../components/DeleteModal.vue';
import Search from '../../../components/Search.vue';
import CreateModal from './CreateModal.vue';
import EditModal from './EditModal.vue';

export default {
  name: 'User',
  components: { 
    CreateModal, 
    EditModal, 
    Pagination,
    DeleteModal,
    Search
  },
  setup() {
    const users = ref([]);
    const isLoading = ref(false);
    const error = ref(null);
    const selectedUser = ref(null);
    const isCreateModalVisible = ref(false);
    const isEditModalVisible = ref(false);
    const currentPage = ref(1);
    const itemsPerPage = ref(10);
    const totalItems = ref(0);

    const startIndex = computed(() => (currentPage.value - 1) * itemsPerPage.value);

    const fetchUsers = async () => {
      isLoading.value = true;
      error.value = null;
      
      try {
        const response = await apiClient.get(`/admin/user/all`, {
          params: {
            page: currentPage.value,
            per_page: itemsPerPage.value
          }
        });
        
        const result = response.data;
        users.value = result.data?.data ?? [];
        totalItems.value = result.data?.total ?? 0;
        currentPage.value = result.data?.current_page ?? 1;
        itemsPerPage.value = result.data?.per_page ?? 10;
      } 
      catch (err) {
        console.error('Error fetching users:', err);
        error.value = err.response?.data?.message ?? 'Failed to load users';
        users.value = [];
      } 
      finally {
        isLoading.value = false;
      }
    };

    const openCreateModal = () => {
      isCreateModalVisible.value = true;
    };

    const openEditModal = (user) => {
      selectedUser.value = user ? { ...user } : {};
      isEditModalVisible.value = true;
    };

    const closeCreateModal = () => {
      isCreateModalVisible.value = false;
    };

    const closeEditModal = () => {
      isEditModalVisible.value = false;
      selectedUser.value = null;
    };

    const handleSaveSuccess = () => {
      fetchUsers();
    };

    const handlePageChange = (page) => {
      currentPage.value = page;
      fetchUsers();
    };

    onMounted(fetchUsers);

    return {
      users,
      isLoading,
      error,
      selectedUser,
      isCreateModalVisible,
      isEditModalVisible,
      currentPage,
      itemsPerPage,
      totalItems,
      startIndex,
      openCreateModal,
      openEditModal,
      closeCreateModal,
      closeEditModal,
      handleSaveSuccess,
      handlePageChange,
    };
  },
};
</script>
