<template>
    <div class="container-fluid">
        <div class="row">
            <GridCard 
              v-for="(roadMap, index) in roadMaps"  
              :key="index" 
              :roadMap="roadMap.road_map"
              :title="roadMap.name_en"
              @create-btn="openCreateModal"
              @edit-btn="openEditModal"
            />
            <!-- @delete-btn="openDeleteModal" -->
             
            <CreateModal
              v-show="isCreateModalVisible"
              :is-visible="isCreateModalVisible"
              @save-success="handleSaveSuccess"
              @close="closeCreateModal"
            />

            <EditModal
              v-if="isEditModalVisible"
              :road-map="selectedRoadMap"
              :is-visible="isEditModalVisible"
              @save-success="handleSaveSuccess"
              @close="closeEditModal"
            />
      
            <!-- <DeleteModal
              v-if="isDeleteModalVisible"
              @save-success="handleSaveSuccess"
              @close="closeDeleteModal"
            /> -->
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';
import CreateModal from './CreateModal.vue';
import EditModal from './EditModal-old.vue';
// import DeleteModal from '../../../components/DeleteModal.vue';
import GridCard from './GridCard.vue'

export default {
  name: 'RoadMap',
  components: { 
    CreateModal,
    EditModal,
    // DeleteModal,
    GridCard 
  },

  setup() {
    const roadMaps = ref([]);
    const isLoading = ref(false);
    const error = ref(null);
    const selectedRoadMap = ref(null);
    const isCreateModalVisible = ref(false);
    const isEditModalVisible = ref(false);
    // const isDeleteModalVisible = ref(false);

    // Ma'lumotlarni yuklash
    const fetchRoadMaps = async () => {
      isLoading.value = true;
      error.value = null;

      try {
        const response = await apiClient.get('/admin/road-map/all');
        roadMaps.value = response.data?.data ?? [];
      } catch (err) {
        error.value = err.response?.data?.message ?? 'Failed to load road maps';
        console.error('Fetch error:', err);
      } finally {
        isLoading.value = false;
      }
    };

    // Create modal
    const openCreateModal = () => {
      isCreateModalVisible.value = true;
    };

    const closeCreateModal = () => {
      console.log('closeCreateModal ishladi');
      isCreateModalVisible.value = false;
    };

    // Edit modal
    const openEditModal = (roadMap) => {
      selectedRoadMap.value = roadMap;
      isEditModalVisible.value = true;
      console.log('selectedRoadMap: ', roadMap);
    };

    const closeEditModal = () => {
      isEditModalVisible.value = false;
      selectedRoadMap.value = null;
    };

    // const openDeleteModal = () => {
    //   isDeleteModalVisible.value = true;
    // };

    // const closeDeleteModal = () => {
    //   isDeleteModalVisible.value = false;
    // };
    // Saqlash muvaffaqiyatli bo'lsa
    const handleSaveSuccess = () => {
      fetchRoadMaps();
      closeCreateModal();
      closeEditModal();
      // closeDeleteModal();
    };

    // O'chirish
    const handleDelete = (roadMap) => {
      // Delete logic will be implemented here
    };

    onMounted(fetchRoadMaps);

    return {
      roadMaps,
      isLoading,
      error,
      selectedRoadMap,
      isCreateModalVisible,
      isEditModalVisible,
      // isDeleteModalVisible,
      openCreateModal,
      closeCreateModal,
      openEditModal,
      closeEditModal,
      // closeDeleteModal,
      handleSaveSuccess,
      handleDelete,
    };
  },
};
</script>

<style scoped>
.text-danger {
  color: #dc3545;
}
</style>