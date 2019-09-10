import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)
export default new Vuex.Store({
	state: {
		loading: 0,
		loggedInUser: {},
		isUserBeingRetrieved: false,
		isUserRetrieved: false
	},
	mutations: {
		fetchLoggedInUser(state, user){
			state.loggedInUser = user
			state.isUserRetrieved = true
		},
		loadingOn (state){
			state.loading++
		},
		loadingOff (state){
			state.loading--
		}
	},
	actions: {
		fetchCurrentUser({ commit, state }){
			if (!state.isUserBeingRetrieved) {
				state.isUserBeingRetrieved = true

				this.commit('loadingOn')
				axios.get('/api/auth/details').then((response) => {
					commit('fetchLoggedInUser', response.data)
					this.commit('loadingOff')
				}).catch((error) => {
					this.commit('loadingOff');
					return new Promise(error)
				});
			}
		}
	}
});