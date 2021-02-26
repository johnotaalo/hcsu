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
		},
		gotoLogin() {
			window.location = "/login"
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
					// this.commit('gotoLogin')
				});
			}
		},
		checkProcessMakerSession({ commit, state }, payload){
			if (!state.isUserBeingRetrieved) {
				state.isUserBeingRetrieved = true

				this.commit('loadingOn')
				axios.get(`/api/auth/details/pm/${payload.user}`).then((response) => {
					commit('fetchLoggedInUser', response.data)
					this.commit('loadingOff')
				}).catch((error) => {
					// console.log(error)
					this.commit('loadingOff');
					this.commit('gotoLogin')
					// return new Promise((error))
				});
			}
		}
	}
});
