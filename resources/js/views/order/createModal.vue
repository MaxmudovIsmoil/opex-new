<template>
  <div class="container">
    <div class="modal fade" id="addOrderModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header pt-2 pb-2">
                    <h5 class="modal-title" id="addLabel"> {{ $t('add_order') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="modal-body pt-2 pb-2">
                        <div class="mb-3">
                            <label for="customer" class="form-label"> {{ $t('client') }}</label>
                            <input type="text" id="customer" class="form-control" name="customer" />
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="address" class="form-label"> {{ $t('address') }}</label>
                                <input type="text" id="address" class="form-control" name="address" />
                            </div>
                            <div class="col-md-6">
                                <label for="equipment_cost" class="form-label"> {{ $t('equipment_and_material_costs') }}</label>
                                <input type="text" id="equipment_cost" class="form-control" name="equipment_cost" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="work_materials" class="form-label"> {{ $t('labor_and_materials') }}</label>
                                <input type="text" id="work_materials" class="form-control" name="work_materials" />
                            </div>

                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="subscription_fee" class="form-label"> {{ $t('subscriber_line_organization_fee') }}</label>
                                <input type="text" id="subscription_fee" class="form-control" name="subscription_fee" />
                            </div>
                            <div class="col-md-6">
                                <label for="monthly_payment" class="form-label"> {{ $t('monthly_payment') }}</label>
                                <input type="text" id="monthly_payment" class="form-control" name="monthly_payment" />
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payback_period" class="form-label"> {{ $t('payback_period') }}</label>
                                <input type="text" id="payback_period" class="form-control" name="payback_period" />
                            </div>
                            <div class="col-md-6">
                                <label for="basis" class="form-label"> {{ $t('basis') }}</label>
                                <input type="text" id="basis" class="form-control" name="basis" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label"> {{ $t('attached_files') }}</label>
                            <input type="file" id="file" class="form-control" name="files[]" multiple />
                        </div>
                    </div>
                    <div class="modal-footer pt-2 pb-2">
                        <input type="submit" class="btn btn-primary" :value="$t('apply')">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> {{ $t('close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</template>

<script>

export default{

}
</script>


<style>

</style>
