<template>
	<div>
		<loading
		:active.sync="loading"
        :can-cancel="false"
        :is-full-page="true"></loading>
		<div class="card">
			<div class="card-body">
				<b-row>
					<b-col>
						<v-select placeholder="Process" :options="processes" v-model="selectedOptions.process"></v-select>
					</b-col>

					<b-col>
						<v-select placeholder="User" :options="users" v-model="selectedOptions.user"></v-select>
					</b-col>

					<b-col cols="auto">
						<b-button size="sm" variant="primary" @click="search">Search</b-button>
					</b-col>
				</b-row>
			</div>

			<v-client-table class="table-sm card-table" :columns="table.columns" :data="table.data" :options="table.options">
				<template slot="ACTIONS" slot-scope="data">
					<b-dropdown text="Actions" size="sm" class="btn-white">
						<b-dropdown-item>Reassign</b-dropdown-item>
						<b-dropdown-item>Route Case</b-dropdown-item>
						<b-dropdown-item>Cancel</b-dropdown-item>
						<b-dropdown-item>Delete</b-dropdown-item>
					</b-dropdown>
				</template>
			</v-client-table>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {
		data(){
			return {
				loading: false,
				table: {
					columns: ["CASE_NO", "CASE_TITLE", "PROCESS", "CREATE_DATE", "ACTIONS"],
					options: {
						filterable: false,
						perPage: 50,
						perPageValues: [],
					},
					columnsDropdown: false,
					pagination: {
						nav: "fixed",
						dropdown: false,
						edge: true,
					},
					data: []
				},
				processes: [],
				users: [],
				selectedOptions: {
					process: "",
					user: ""
				}
			}
		},
		mounted(){
			this.loading = true
			let _this = this
			axios.all([this.getProcesses(), this.getUsers()])
			.then(axios.spread(function(processRes, usersRes){
				_this.loading = false
				_this.processes = _.map(processRes.data, (process) => {
					return {
						'uid'	: process.PRO_UID,
						'label'	: process.PRO_TITLE
					}
				})
				_this.users = _.map(usersRes.data, (user) => {
					return {
						'uid'	: user.USR_UID,
						'label'	: `${user.USR_LASTNAME}, ${user.USR_FIRSTNAME}`
					}
				})
			}))
		},
		methods: {
			getProcesses: function(){
				return axios.get("/api/data/processes/local");
			},
			getUsers: function(){
				return axios.get("/api/data/users");
			},
			search: function(){
				let _process = this.selectedOptions.process.uid
				let _user = this.selectedOptions.user.uid

				axios.post('/api/processmaker/case/search', {process: _process, user: _user})
				.then(res => {
					this.table.data = _.map(res.data, (_case) => {
						return {
							"CASE_NO"		: _case.APP_NUMBER,
							"CASE_TITLE"	: _case.APP_TITLE,
							"PROCESS"		: _case.process.CON_VALUE,
							"CREATE_DATE"	: _case.APP_CREATE_DATE
						}
					})
				})
				.catch(error => {
					this.$swal('Error', error.message, 'error')
				})
			}
		}
	}
</script>