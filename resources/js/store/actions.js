
import axios from 'axios'

export default {
  async fetchItems({ commit }) {
    commit('setLoading', true)
    try {
      const response = await axios.get('https://api.example.com/items')
      commit('setItems', response.data)
      commit('setError', null)
    } catch (error) {
      commit('setError', error)
    } finally {
      commit('setLoading', false)
    }
  }
}
