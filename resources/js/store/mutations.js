// store/mutations.js
export default {
    setItems(state, items) {
      state.items = items
    },
    setLoading(state, loading) {
      state.loading = loading
    },
    setError(state, error) {
      state.error = error
    }
  }
  