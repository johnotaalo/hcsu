<template>
	<div>
		<b-card>
			<div class="row align-items-center">
				<div class="col">
					<div class="row align-items-center">
						<div class="col">
							<b-input type="search" class="form-control" v-model = "caseNo" placeholder="Please Input Case Number"/>
						</div>
					</div>
				</div>
				<div class="col-auto">
					<b-button class= "float-right" variant="primary" size ="sm" @click="searchCase"><i class="fe fe-search"></i>&nbsp;Search Case</b-button>
				</div>
			</div>

			<div class="row">
				<loading
				:active.sync="loading"
		        :can-cancel="false"
		        :is-full-page="false"></loading>
			</div>

			<div class="row mt-3" v-if="!empty">
				<!-- <div class="row"> -->
				<div class="col-md-12">
					<p><strong>Current User:</strong> {{ caseData.current_user.USR_LASTNAME }}, {{ caseData.current_user.USR_FIRSTNAME }}</p>
					<label class="control-label">Reassign the case to:</label>
					<v-select v-model = "selectedUser" :options="users" :class="form-control">
						<template slot="no-options">
							Type to search for a user
						</template>
						<template slot="option" slot-scope="option">
							{{ option.USR_LASTNAME }}, {{ option.USR_FIRSTNAME }}
						</template>

						<template slot="selected-option" slot-scope="option">
							{{ option.USR_LASTNAME }}, {{ option.USR_FIRSTNAME }}
						</template>
					</v-select>
				</div>

				<div class="col-md-12 mt-2">
					<b-button class = "btn btn-success btn-sm" @click="reassignCase">Reassign Case</b-button>
				</div>
				
				<!-- </div> -->
			</div>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../core/Form'
	export default {
		data(){
			return {
				caseNo: 0,
				caseData: "",
				loading: false,
				users: [],
				selectedUser: ""
			}
		},
		mounted(){
			this.getUsers()
		},
		methods: {
			searchCase: function(){
				this.loading = true
				this.caseData = {}
				axios.get(`/api/data/processes/oldpm/search/case/${this.caseNo}`)
				.then(res => {
					this.caseData = res.data
					this.loading = false
				})
				.catch(error => {
					this.loading = false
					console.log(error)
				});
			},
			getUsers: function(){
				axios.get('api/data/processes/oldpm/users')
				.then(res => {
					this.users = res.data
				});
			},
			reassignCase: function(){
				this.loading = true
				axios.post('api/data/processes/oldpm/reassign', { APP_UID: this.caseData.APP_UID, USR_UID: this.selectedUser.USR_UID })
				.then(res => {
					this.loading = false
					console.log(res)
				})
				.catch(e => {
					this.loading = false
					console.log(e)
				});
			}
		},
		computed: {
			empty: function(){
				for(var key in this.caseData) {
					if(this.caseData.hasOwnProperty(key))
						return false;
				}
				return true;
			}
		}
	}
</script>