<template>
	<div class="card">
		<div class="card-header">
			<h4 class="card-title"><i class="fe fe-user-plus"></i>&nbsp;&nbsp;Add User</h4>
		</div>

		<div class="card-body">
			<div class="row">
				<div class="col-12 col-md-6">
					<b-form-group
					label="First Name"
					label-for="firstname">
						<b-form-input id="firstname" v-model="form.firstname"></b-form-input>
					</b-form-group>
				</div>
				<div class="col-12 col-md-6">
					<b-form-group
					label="Last Name"
					label-for="lastname">
						<b-form-input id="lastname" v-model="form.lastname"></b-form-input>
					</b-form-group>
				</div>

				<div class="col-12">
					<b-form-group
					label="Email"
					label-for="email">
						<b-form-input id="email" v-model="form.email"></b-form-input>
					</b-form-group>
				</div>

				<div class="col-12 col-md-6">
					<b-form-group
					label="User Type"
					label-for="user_type">
						<b-select :options="userTypes" v-model="form.user_type"></b-select>
					</b-form-group>
				</div>

				<div class="col-12 col-md-6">
					<b-form-group
					label="Username"
					label-for="username"
					description="Should be the same as Active Directory Username if we intend to use the AD">
						<b-form-input id="username" v-model="form.username"></b-form-input>
					</b-form-group>
				</div>

				<div class="col-12 col-md-6">
					<b-form-group
					label="Password"
					label-for="password">
						<b-form-input id="password" type="password" v-model="form.password"></b-form-input>
					</b-form-group>
				</div>
			</div>
			<b-button variant="primary" @click="submitForm">Save changes</b-button>
		</div>
	</div>
	
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	export default{
		data(){
			return {
				userTypes: [],
				form: new Form({
					firstname: "",
					lastname: "",
					email: "",
					user_type: "",
					username: "",
					password: ""
				})
			}
		},
		created(){
			this.getUserTypes()
		},
		methods: {
			getUserTypes: function(){
				axios.get('/api/users/usertypes')
				.then(res => {
					this.userTypes = _.map(res.data, (val, text) => {
						return {
							value: val,
							text: text
						}
					})
				})
				.catch(error =>{
					this.$swal('Error', error.message, "error")
				})
			},
			submitForm: function(){
				this.form.post("/users/add")
				.then(res => {
					this.$swal("Success", "Successfully added user", "success")
					.then(() => {
						this.$router.push({ name: "users" })
					})
				})
				.catch(error => {
					this.form.errors = error
				})
			}
		},
		computed: {

		}
	}
</script>