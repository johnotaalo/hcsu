<template>
	<div>
		<div class="row">
			<div class="col-md-3 offset-9">
				<b-button variant="primary" class = "float-right" v-b-modal.add-batch-modal><i class="fe fe-plus"></i>&nbsp;&nbsp;Add Batch</b-button>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<v-client-table :columns="columns" :data="batches">
					<template slot="#" slot-scope="data">
						{{ data.index }}
					</template>

					<template slot="Batch Date" slot-scope="data">
						{{ data.row.batch_date | moment("dddd, MMMM Do YYYY")}}
					</template>

					<template slot="Comment" slot-scope="data">
						{{ data.row.comment | nullable }}
					</template>

					<template slot="Status" slot-scope="data">
						<span v-bind:class="[ data.row.is_active ? 'text-success' : 'text-danger' ]">●</span><span v-if="data.row.is_active"> Active</span><span v-else>Inactive</span>
					</template>

					<template slot="Cases" slot-scope="data">
						<a>{{ data.row.cases.length }} case(s)</a>
					</template>

					<template slot="Actions" slot-scope="data">
						<div class="dropdown show">
							<a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Actions <i class="fe fe-more-vertical"></i>
							</a>
							<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(-135px, 25px, 0px); top: 0px; left: 0px; will-change: transform;">
								<a class="dropdown-item" v-if="data.row.cases.length > 0" @click="downloadVATList(data.row.id, data.row.batch_date)">
									Download List
								</a>
								<a class="dropdown-item" v-if="data.row.cases.length == 0" @click="deleteBatch(data.index - 1)">
									Delete
								</a>
								<a class="dropdown-item">
									View Cases
								</a>
							</div>
						</div>
					</template>
				</v-client-table>
			</div>
		</div>

		<b-modal id="add-batch-modal" centered title="Add Batch" no-close-on-backdrop @ok="submitForm">
			<b-form-group label="Batch Date">
				<datetime v-model="form.batch_date" input-class="form-control" :min-datetime="new $moment().format()" format="dd-MM-yyyy"></datetime>
			</b-form-group>

			<b-form-group label="Comment">
				<b-textarea v-model="form.comment"></b-textarea>
			</b-form-group>
		</b-modal>
	</div>
</template>

<script type="text/javascript">
	import Form from '../../../core/Form'
	import { Datetime } from 'vue-datetime'

	export default {
		components: { datetime: Datetime, Form },
		data(){
			return {
				columns: ['#', 'Batch Date', 'Comment', 'Status', 'Cases', 'Actions'],
				batches: [],
				form: new Form({
					batch_date: "",
					comment: ""
				})
			}
		},
		created(){
			this.getBatches();

			this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
				this.clearModal()
			})
		},
		methods: {
			getBatches(){
				axios.get('/api/vat/blanket/batches')
				.then(res => {
					this.batches = res.data
				})
			},
			clearModal(){
				this.form.batch_date = ""
				this.form.comment = ""
			},
			submitForm(){
				this.form.post('/vat/blanket/batch')
				.then(res => {
					this.batches.unshift(res)
				})
				.catch(error => {
					console.log(error)
				});
			},
			deleteBatch: function(row){
				
				this.$swal({
					title: "Delete Batch?", 
					text: `Are you sure you would like to delete this item?`, 
					icon: 'warning',
					dangerMode: true,
					buttons: true
				})
				.then((willDelete) => {
					if (willDelete) {
						this.proceedDelete(row)
					}
				})
			},
			proceedDelete(row){
				var value = this.batches[row]
				axios.delete('/api/vat/blanket/batch', { data: { id: value.id } })
				.then(res => {
					this.$swal('Success!', 'Successfully deleted item', 'success')
					this.batches.splice(row, 1)
				})
				.catch(error => {
					this.$swal("Error!", `Error: ${error.response.data.message}`, "error");
				})
			},
			downloadVATList: function(batch_id, batch_date){
				var url = `/api/vat/blanket/list/download/${batch_id}`
				axios({
					url: url, 
					method: 'GET',
					responseType: 'blob',
				})
				.then(res => {
					const url = window.URL.createObjectURL(new Blob([res.data]));
					const link = document.createElement('a');
					link.href = url;
					link.setAttribute('download', `BLANKET VAT LIST BATCH (${batch_date}).xlsx`); //or any other extension
					document.body.appendChild(link);
					link.click();
				});
			}
		}
	}
</script>