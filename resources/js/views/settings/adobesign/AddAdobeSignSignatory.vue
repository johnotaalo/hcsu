<template>
	<div>
		<b-card>
			<div class="row">
				<div class="col-md">
					<b-form-group label="Last Name">
						<b-input v-model="form.last_name"></b-input>
					</b-form-group>
				</div>
				<div class="col-md">
					<b-form-group label="Other Names">
						<b-input v-model="form.other_names"></b-input>
					</b-form-group>
				</div>
			</div>
			

			<b-form-group label="Email">
				<b-input v-model="form.email"></b-input>
			</b-form-group>

			<b-form-checkbox value="active" unchecked-value="inactive" v-model="form.status">
				Mark Signatory as active
			</b-form-checkbox>

			<b-button class = "btn btn-sm btn-white mt-5" @click="save"><i class = "fe fe-check"></i>&nbsp;Save</b-button>
		</b-card>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'

	export default{
		data(){
			return {
				form: new Form({
					id: 0,
					other_names: "",
					last_name: "",
					email: "",
					status: "inactive"
				})
			}
		},
		created(){
			if (this.$route.params.id) {
				this.form.id = this.$route.params.id
				this.getData()
			}
		},
		methods: {
			save(){
				if(this.form.id == 0){
					this.form.post('data/adobe-sign/signatories')
					.then(res => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: 'Successfully added signatory',
							type: "success"
						});
						this.$router.push({name: 'settings-signatories'});
					})
					.catch(error => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: error.message,
							type: "error"
						});

						console.log(error)
					});
				}else{
					this.form.put('data/adobe-sign/signatories')
					.then(res => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: 'Successfully updated signatory',
							type: "success"
						});
						this.$router.push({name: 'settings-signatories'});
					})
					.catch(error => {
						this.$notify({
							group: 'foo',
							title: 'Success',
							text: error.message,
							type: "error"
						});

						console.log(error)
					});
				}
			},
			getData(){
				axios.get(`api/data/adobe-sign/signatories/get/${this.form.id}`)
				.then(res => {
					this.form.other_names = res.data.other_names
					this.form.last_name = res.data.last_name
					this.form.email = res.data.email
					this.form.status = (res.data.status) ? "active" : "inactive"
				});
			}
		}
	}
</script>