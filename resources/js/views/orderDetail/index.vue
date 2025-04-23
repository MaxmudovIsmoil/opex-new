<template>
<div class="container-fluid">
  <div class="d-flex justify-content-between mb-4">
    <div class="d-flex">
      <div class="me-2">
        <router-link  class="btn btn-secondary btn-sm" to="/orders">
          <font-awesome-icon :icon="['fas', 'arrow-left']" /> {{ $t('back') }}
        </router-link>
      </div>
      <div class="me-2">
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" @click="message='agreed',messageColor='text-success'">
          <font-awesome-icon :icon="['fas', 'circle-check']" /> {{ $t('agree') }}
        </button>
      </div>
      <div>
        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" @click="message='declined', messageColor='text-danger'">
          <font-awesome-icon :icon="['fas', 'times-circle']" /> {{ $t('decline') }}
        </button>
      </div>
    </div>
    <div>
      <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <font-awesome-icon :icon="['fas', 'paperclip']" /> {{ $t('attach_file') }}
      </button>
    </div>
  </div>

 
  <div class="card bg-shadow mb-4">
    <div class="card-body">
      <h5>{{ $t('order_subjects') }}</h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>№</th>
              <th>{{ $t('name') }}</th>
              <th>{{ $t('unit') }}</th>
              <th>{{ $t('amount') }}</th>
              <th>{{ $t('status') }}</th>
              <th>{{ $t('capex') }}</th>
              <th>{{ $t('cost_hypothetical') }}</th>
              <th>{{ $t('purpose') }}</th>
              <th>{{ $t('place_of_installation') }}</th>
              <th>{{ $t('time_of_installation') }}</th>
              <th>{{ $t('contract_number') }}</th>
              <th>{{ $t('contract_date') }}</th>
              <th>{{ $t('comment') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in OrderSubjects" :key="index">
              <td>{{ ++index }}</td>
              <td>{{ item.Name }}</td>
              <td>{{ item.Unit }}</td>
              <td>{{ item.Amount }}</td>
              <td>{{ item.Status }}</td>
              <td>{{ item.Capex }}</td>
              <td>{{ item.Cost_hypothetical }}</td>
              <td>{{ item.Purpose }}</td>
              <td>{{ item.Place_of_Installation }}</td>
              <td>{{ item.Time_of_Installation }}</td>
              <td>{{ item.Contract_number }}</td>
              <td>{{ item.Contract_date }}</td>
              <td>{{ item.Comment }}</td>

            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="card bg-shadow mb-4">
    <div class="card-body">
      <h5>{{ $t('actions_under_order') }}</h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>№</th>
              <th>{{ $t('time') }}</th>
              <th>{{ $t('user') }}</th>
              <th>{{ $t('instance') }}</th>
              <th>{{ $t('instance_reply') }}</th>
              <th>{{ $t('instance_replied_in') }}</th>
              <th>{{ $t('comment') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>23 Jul 2024 14:40:47</td>
              <td>Temur Butaev</td>
              <td>Example Instance</td>
              <td>Reply</td>
              <td>Time of Reply</td>
              <td>Comment</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <div class="card bg-shadow mb-4">
    <div class="card-body">
      <h5>{{ $t('actions_under_subjects') }}</h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>№</th>
              <th>№ {{ $t('subject') }}</th>
              <th>{{ $t('executed') }}</th>
              <th>{{ $t('user') }}</th>
              <th>{{ $t('description') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>1</td>
              <td>Executed</td>
              <td>Temur Butaev</td>
              <td>Description</td>
            </tr>
            <tr>
              <td colspan="5">01 Aug 2024 15:59:09, Temur Butaev, Creator</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card bg-shadow mb-4">
    <div class="card-body">
      <h5>{{ $t('passage_plan') }}</h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>{{ $t('instance') }}</th>
              <th>{{ $t('stage') }}</th>
              <th>{{ $t('period_of_consideration') }}</th>
              <th>{{ $t('instance_users') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Test</td>
              <td>1</td>
              <td>8 working hours</td>
              <td>Temur Butaev</td>
            </tr>
            <tr>
              <td>Test 2</td>
              <td>2</td>
              <td>8 working hours</td>
              <td>Isroil Maxmudov</td>
            </tr>
            <tr>
              <td>Test 3</td>
              <td>3</td>
              <td>8 working hours</td>
              <td>Isroil Maxmudov, Temur Butaev</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <OrderConfirmModal :message="message"  :messageColor="messageColor"/>
  
  <UploadModal/>
  
  <DeleteModal/>
</div>

</template>

<script>
import UploadModal from './components/UploadModal.vue';
import OrderConfirmModal from './components/OrderConfirmModal.vue';
import data from '../../fake-data/orders.json';
import DeleteModal from '../../components/DeleteModal.vue';
export default{
  name: 'OrderDetail',
  components:{
    UploadModal,
    OrderConfirmModal,
    DeleteModal
  },
  data(){
    return{
      message: null,
      messageColor: null,
      OrderSubjects: data.orderSubjects
    }
  },

}
</script>
<style>

</style>
