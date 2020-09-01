<template>
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-default" @click="create()"><span class="glyphicon glyphicon-file"></span></button>
        <button type="button" class="btn btn-default" @click="save()"><span class="glyphicon glyphicon-floppy-disk"></span></button>
        <button type="button" class="btn btn-default" @click="load()"><span class="glyphicon glyphicon-refresh"></span></button>
    </div>
</template>

<script>
    import { Events } from './../events';

    export default {
        mounted() {
            let self = this;

            window.axios.interceptors.request.use((request) => {
                window.jQuery("button").attr("disabled", "disabled");
                return request;
            });

            window.axios.interceptors.response.use((response) => {
                window.jQuery("button").removeAttr("disabled");
                return response;
            }, error => {
                window.jQuery("button").removeAttr("disabled");

                self.$notify({
                    title: "Exception",
                    type: "error",
                    text: error.response.data.message,
                    group: "exceptions",
                    duration: 15000
                });

                return Promise.reject(error.response);
            });
        },

        methods: {
            create() {
                Events.$emit('toolbar.create');
            },

            save() {
                Events.$emit('toolbar.save');
            },

            load() {
                Events.$emit('toolbar.load');
            }
        }
    }
</script>
