// store/index.js
import { createStore } from 'vuex'
import state from './state.js'
import mutations from './mutations.js'
import actions from './actions.js'
import getters from './getters.js'

const store = createStore({
  state,
  mutations,
  actions,
  getters
})

export default store
