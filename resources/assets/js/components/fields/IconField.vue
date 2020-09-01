<template>
    <div class="form-group icon-field">
        <select class="form-control selectpicker" data-live-search="true" v-model="selected">
            <option :value="null">NULL</option>
            <option v-for="icon in icons" :value="icon.unityPath" :selected="icon.unityPath === selected" :data-content="'<img style=\'height: 40px;\' src=' + icon.url + '>' + ' ' + icon.unityPath">
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                selected: "",
                icons: window.library.icons
            }
        },

        mounted() {
            this.selected = this.$props.value;
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
        },

        props: ["value"],

        updated () {
            window.$(this.$el.querySelector('select')).selectpicker('refresh');
        },

        watch: {
            selected: function (value) {
                this.$emit('input', value);
            },

            value: function (value) {
                this.selected = value;
            }
        }
    };
</script>

<style>
    .icon-field > .bootstrap-select {
        height: 60px;
    }

    .icon-field > .bootstrap-select > .dropdown-toggle {
        height: 60px;
    }
</style>
