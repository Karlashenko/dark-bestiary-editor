<template>
    <select v-model="selected" ref="select" class="form-control selectpicker" :multiple="multiple" data-live-search="true">
        <option v-if="nullable" :value="null">NULL</option>
        <option v-for="option in options" :value="valuecallback ? valuecallback(option) : option" :selected="selectcallback ? selectcallback(selected, option) : option === selected">
            {{ labelcallback ? labelcallback(option) : option }}
        </option>
    </select>
</template>

<script>
    export default {
        props: ["value", "nullable", "options", "multiple", "labelcallback", "valuecallback", "selectcallback"],

        data() {
            return {
                selected: this.$props.value
            };
        },

        mounted() {
            window.$(this.$refs.select).selectpicker("refresh");
        },

        updated() {
            window.$(this.$refs.select).selectpicker("refresh");
        },

        watch: {
            selected: function (value) {
                this.$emit("input", value);
            },

            value: function (value) {
                this.selected = value;
            }
        }
    };
</script>
