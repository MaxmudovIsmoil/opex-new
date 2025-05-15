<template>
  <div class="container-fluid">
    <div class="bg-shadow padding-15">
      <div class="d-flex align-items-center">
        <div>
          <a class="btn btn-primary btn-sm js_add_btn mb-2" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <font-awesome-icon :icon="['fas', 'plus']" /> {{ $t('add') }}
          </a>
        </div>
        <div class="ms-3 mb-2">
          <div class="form-check form-switch">
            <input class="form-check-input js_hide_show_tr_btn" @change="showActual"
              data-status="1" type="checkbox" role="switch" id="actuallyOrders">
            <label class="form-check-label" for="actuallyOrders">{{ $t('actual') }}</label>
          </div>
        </div>
        <div class="col mb-2">
            <Search/>
        </div>
      </div>
      <table id="order_datatable" class="table table-bordered table-fs-sm table-striped table-responsive" style="width:100%;">
        <thead>
          <tr>
            <th class="text-center">{{ $t('id') }}</th>
            <th>{{ $t('type') }}</th>
            <th>{{ $t('customer_address') }}</th>
            <th>{{ $t('secondary_recipients') }}</th>
            <th>{{ $t('status') }}</th>
            <th>{{ $t('current_instance') }}</th>
            <th>{{ $t('stage') }}</th>
            <th>{{ $t('deadline') }}</th>
            <th>{{ $t('comment') }}</th>
            <th>{{ $t('created') }}</th>
          </tr>
        </thead>
        <tbody>
          <tableRow v-for="(item, index) in paginatedItems" :key="index" :order="item" :index="index + 1" />
        </tbody>
      </table>
      <div class="d-flex justify-content-center">
        <Pagination
          :totalItems="showOrders.length"
          :itemsPerPage="itemsPerPage"
          v-model:currentPage="currentPage"
        />
      </div>
    </div>
    <createModal/>

  </div>
</template>

<script>
import tableRow from './tableRow.vue';
import createModal from './createModal.vue';
import Pagination from '../../components/Pagination.vue';
import data from '../../fake-data/orders.json';
import Search from '../../components/Search.vue';

export default {
  name: 'Orders',
  components: {
    tableRow,
    createModal,
    Pagination,
    Search
  },
  data() {
    return {
      currentPage: 1,
      itemsPerPage: 6,
      orders: [],
      showOrders: []
    };
  },
  created() {
    this.orders = data.orders;
    this.showOrders = this.orders;
  },
  computed: {
    paginatedItems() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.showOrders.slice(start, end);
    }
  },
  methods: {
    showActual(event) {
      const isChecked = event.target.checked;
      if (isChecked) {
        this.showOrders = this.orders.filter(order => {
          return order.status === "Тест 1" || order.status === "Тест 2";
        });
      } else {
        this.showOrders = this.orders;
      }
      this.currentPage = 1;
    }
  }
};
</script>
