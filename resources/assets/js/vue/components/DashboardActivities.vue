<template>
    <div class="panel panel-dashboard">
        <div class="nav dashboard-heading">
            <ul class="nav navbar-nav navbar-left dashboard-nav">
                <li v-for="(title, index) in tabs"
                    @click.prevent="setType(index)" 
                    :class="{ active: type === index }"
                >
                    <a :title="title" href="#">
                        {{ title }}
                    </a>
                </li>
            </ul>
            <div class="new-activity">
                <span class="amount">{{ newToday }}</span>
                <span>New Today</span>
            </div>
        </div>
        <div class="panel-body panel-days-holder">
            <div v-show="!first" class="day static">
                <div @click="previous" class="day-number">
                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                </div>
                <div class="border"></div>
            </div>

            <div v-for="day in days" class="day">
                <div class="day-number">
                    {{ formattedDay(day) }}
                </div>
                <div class="border"></div>
                <div class="events">
                    <div v-for="activity in activitiesByDay[day]" class="event">
                        <span class="time">{{ activity.time }}</span>
                        <span v-html="activity.message"></span>
                    </div>
                </div>
            </div>

            <div v-show="!last" class="day static">
                <div @click="next" class="day-number">
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                </div>
                <div class="border"></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['activities', 'state'],

        data() {
            return {
                newToday: 0,
                first: true,
                last: false,
                type: 0,
                from: 0,
                items: []
            };
        },

        computed: {

            tabs() {
                return [
                    'All Activity',
                    'Payments',
                    'Expenses',
                    'Upcoming Invoices',
                    'Invoices Past Due',
                    'Tasks',
                    'Projects'
                ];
            },

            days() {
                let days = [];

                this.items.forEach(activity => {
                    let created_at = moment(activity.created_at.date);

                    if (days.indexOf(created_at.date()) < 0 && days.length < 3) {
                        days.push(created_at.date());
                    }
                });

                return days.sort((a, b) => b - a);
            },


            activitiesByDay() {
                let days = {};

                this.items.forEach(activity => {
                    let created_at = moment(activity.created_at.date);

                    if (typeof days[created_at.date()] === 'undefined') {
                        days[created_at.date()] = [];
                    }
                    days[created_at.date()].push({
                        time: created_at.format('HH:mm'),
                        message: activity.message
                    });
                });

                return days;
            }
        },

        methods: {
            formattedDay(day) {
                if (day !== 11 && (day - 1) % 10 === 0) {
                    return day + 'st';
                }
                if (day !== 12 && (day - 2) % 10 === 0) {
                    return day + 'nd';
                }
                if (day !== 13 && (day - 3) % 10 === 0) {
                    return day + 'rd';
                }
                return day + 'th';
            },

            setType(type) {
                this.type = type;
                this.from = 0;

                this.$http.get(`/api/activities-list/${this.type}/${this.from}`)
                    .then(response => response.data)
                    .then(data => {
                        this.first = data.first;
                        this.last = data.last;
                        this.items = data.items;
                    });
            },

            previous() {
                this.from -= 12;
                if (this.from < 0) {
                    this.from = 0;
                }

                this.$http.get(`/api/activities-list/${this.type}/${this.from}`)
                    .then(response => response.data)
                    .then(data => {
                        this.first = data.first;
                        this.last = data.last;
                        this.items = data.items;
                    });
            },

            next() {
                this.from += 12;

                this.$http.get(`/api/activities-list/${this.type}/${this.from}`)
                    .then(response => response.data)
                    .then(data => {
                        this.first = data.first;
                        this.last = data.last;
                        this.items = data.items;
                    });
            }
        },

        mounted() {
            this.first = this.state.first;
            this.last = this.state.last;
            this.items = this.activities;

            let today = moment();
            this.newToday = this.items.filter(activity => moment(activity.created_at.date).isSame(today, 'day')).length;
        }
    }
</script>