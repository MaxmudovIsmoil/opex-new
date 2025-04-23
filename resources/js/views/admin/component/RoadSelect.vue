<template>
    <div class="form-group">
      <label :for="inputId" class="form-label">{{ $t('department') }}</label>
      <select
        :id="inputId"
        class="form-control"
        :value="modelValue"
        :disabled="disabled"
        @input="$emit('update:modelValue', Number($event.target.value) || null)"
      >
      <option value="">-</option>
        <option v-for="road in roads" :key="road.id" :value="road.id">
          {{ road.name_en }}
        </option>
      </select>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  import apiClient from '@/axios';
  
  export default {
    name: 'RoadSelect',
    props: {
      modelValue: { type: [String, Number], default: null },
      disabled: { type: Boolean, default: false },
    },
    emits: ['update:modelValue'],
    setup() {
        const inputId = ref('');
        const roads = ref([]);

        onMounted(() => {
            inputId.value = `road-${Math.random().toString(36)}`;
        });

        const fetchData = async () => {
        try {
          const [roadRes] = await Promise.all([
            apiClient.get('/admin/department/all'),
          ]);
          roads.value = roadRes.data?.data ?? [];
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      };

      fetchData();

    return { inputId, roads };
    }
  };
  </script>