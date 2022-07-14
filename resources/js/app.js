import './bootstrap';
import { Timeline, DataSet } from 'vis-timeline/standalone'

var container = document.getElementById('timeline');

function articleTransformer(item) {
    return {
        start: new Date(item.date),
        content: `<a href="${item.url}" target="_blank">${item.title}</a>`,
        className: 'article'
    }
}
function hrbTransformer(item) {
    return {
        start: new Date(item.event_date),
        content: `<a href="https://www.handelsregisterbekanntmachungen.de/skripte/hrb.php?rb_id=${item.rb_id}&land_abk=${item.state}" target="_blank">${item.information.substring(0, 40)}</a>`,
        className: 'hrb'
    }
}

var items = new DataSet([..._.map(window.articles, articleTransformer), ..._.map(window.hrbs, hrbTransformer)]);

var options = {
    width: '100%',
    height: '90vh',
    margin: {
        item: 20
    },
    selectable: false,
    min: new Date(1998, 1, 1),
    max: new Date(2022, 5, 23),
    preferZoom: true,
};

var timeline = new Timeline(container, items, options);
