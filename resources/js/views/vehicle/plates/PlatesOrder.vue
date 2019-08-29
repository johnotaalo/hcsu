<template>
	<div>
		<div class="header">
			<div class="header-body">
				<div class="row align-items-center">
					<div class="col">
						<!-- Pretitle -->
						<h6 class="header-pretitle">
							Vehicle Plates
						</h6>
						<!-- Title -->
						<h1 class="header-title">
							Order Plates
						</h1>
					</div>
				</div> <!-- / .row -->
			</div>
		</div>

		<div class="row">
			<div class="col-md-7">
				<div class="card">
					<div class="card-header">
						<div class="row align-items-center">
							<div class="col">

								<!-- Title -->
								<h4 class="card-header-title">
									Orders
								</h4>

							</div>
							<div class="col-auto">

								<!-- Button -->
								<a @click="openComponent({}, 'add')" class="btn btn-sm btn-white">
									Add Prefix
								</a>

							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-5"></div>
		</div>
	</div>
</template>

<script type="text/javascript">
	export default {

	}
</script>