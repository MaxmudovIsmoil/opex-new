<template>
  <nav class="pagination">
    <button
      :disabled="currentPage === 1"
      @click="$emit('change-page', currentPage - 1)"
      class="btn btn-sm btn-outline-primary me-2"
    >
    <font-awesome-icon :icon="['fas', 'backward']" />
    </button>

    <button
      v-for="page in totalPages"
      :key="page"
      :class="['btn', 'btn-sm', currentPage === page ? 'btn-primary' : 'btn-outline-primary', 'mx-1']"
      @click="$emit('change-page', page)"
    >
      {{ page }}
    </button>

    <button
      :disabled="currentPage === totalPages"
      @click="$emit('change-page', currentPage + 1)"
      class="btn btn-sm btn-outline-primary ms-2"
    >
      <font-awesome-icon :icon="['fas', 'forward']" />
    </button>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true,
    },
    totalItems: {
      type: Number,
      required: true,
    },
    itemsPerPage: {
      type: Number,
      default: 10,
    },
  },
  computed: {
    totalPages() {
      return Math.ceil(this.totalItems / this.itemsPerPage);
    },
  },
};
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1rem;
}
.btn:disabled {
  opacity: 0.5;
}
</style>