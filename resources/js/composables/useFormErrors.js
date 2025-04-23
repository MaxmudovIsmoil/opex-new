import { ref } from 'vue';

export function useFormErrors() {
  const errors = ref({});

  const clearErrors = () => {
    errors.value = {};
  };

  const clearFieldError = (fieldName) => {
    if (errors.value[fieldName]) {
      delete errors.value[fieldName];
    }
  };

  return {
    errors,
    clearErrors,
    clearFieldError,
  };
}
