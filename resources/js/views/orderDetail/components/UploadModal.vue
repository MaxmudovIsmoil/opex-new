<template>
  <div>
    <div class="modal fade" id="uploadModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="fileUploadModalLabel">{{ $t('upload') }} {{ $t('file') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="handleSubmit">
              <div class="row">
                <div class="col-md-10 mb-3">
                  <label for="fileInput" class="form-label">{{ $t('select_file') }}</label>
                  <input type="file" id="fileInput" class="form-control" ref="fileInput" @change="handleFileChange">
                </div>
                <div class="col-md-2" style="margin-top: 32px">
                  <button type="submit" class="btn btn-primary btn-sm me-1" style="width: 100%; font-size: 16px;">
                    <font-awesome-icon :icon="['fas', 'file-upload']" />  {{ $t('upload') }}
                  </button>
                </div>
              </div>
            </form>

            <div class="mt-4">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th width="4%" class="text-center">№</th>
                    <th>{{ $t('department') }}</th>
                    <th>{{ $t('user') }}</th>
                    <th>{{ $t('file') }}</th>
                    <th width="10%">{{ $t('time') }}</th>
                    <th class="text-center" width="8%">{{ $t('actions') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(file, index) in uploadedFiles" :key="index">
                    <td class="text-center">{{ index + 1 }}</td>
                    <td>{{ getRandomText() }}</td>
                    <td>{{ getRandomText() }}</td>
                    <td>
                      <a @click="downloadFile(file.url)" target="_blank" class="btn btn-outline-primary me-2 btn-sm">
                        <font-awesome-icon :icon="['fas', 'fa-download']" /> {{ file.name }}
                      </a>
                    </td>
                    <td>{{ file.uploadTime }}</td>
                    <td class="text-center">
                      <button class="btn btn-danger btn-sm" @click="deleteFile(index)">
                        <font-awesome-icon :icon="['fas', 'fa-trash-alt']" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ $t('close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      file: null,
      uploadedFiles: [
        {
          name: 'File 1',
          url: 'https://example.com/file1.pdf',
          uploadTime: '2023-06-01 10:00:00',
        },
        {
          name: 'File 2',
          url: 'https://example.com/file2.doc',
          uploadTime: '2023-06-02 12:30:00',
        },
      ],
    };
  },
  methods: {
    handleFileChange(event) {
      this.file = event.target.files[0];
    },
    handleSubmit() {
      if (this.file) {
        const fileUrl = URL.createObjectURL(this.file);
        this.uploadedFiles.push({
          name: this.file.name,
          url: fileUrl,
          uploadTime: new Date().toLocaleString(),
        });
        this.$refs.fileInput.value = '';
        this.file = null;
      }
    },
    getRandomText() {
      const texts = ['Department A', 'Department B', 'Department C'];
      return texts[Math.floor(Math.random() * texts.length)];
    },
    deleteFile(index) {
      this.uploadedFiles.splice(index, 1); 
    },
  },
};
</script>

<style scoped>
.modal-dialog {
  max-width: 70vw;
}
</style>
