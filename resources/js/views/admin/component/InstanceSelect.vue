<template>
    <div class="form-group">
      <label :for="inputId" class="form-label">{{ $t('instance') }}</label>
      <select
        :id="inputId"
        class="form-control"
        :value="modelValue"
        :disabled="disabled"
        @input="$emit('update:modelValue', Number($event.target.value) || null)"
        :class="{'is-invalid': error}"
      >
        <option value="">-</option>
        <option v-for="instance in instances" :key="instance.id" :value="instance.id">
          {{ instance.name_en }}
        </option>
      </select>
    </div>
  </template>
  
<script>
import { ref, onMounted } from 'vue';
import apiClient from '@/axios';

export default {
  name: 'InstanceSelect',
  props: {
    modelValue: { type: [String, Number], default: null },
    disabled: { type: Boolean, default: false },
    error: { type: [String, Boolean], required: false },
  },
  emits: ['update:modelValue'],
  setup() {
      const inputId = ref('');
      const instances = ref([]);

      onMounted(() => {
          inputId.value = `instance-${Math.random().toString(36)}`;
      });
  

      const fetchData = async () => {
        try {
          const [instanceRes] = await Promise.all([
            apiClient.get('/admin/instance/get'),
          ]);
          instances.value = instanceRes.data?.data ?? [];
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      };

      fetchData();

    return { inputId, instances };
  }
};
</script>