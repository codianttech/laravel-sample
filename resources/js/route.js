import route from 'ziggy';
import { Ziggy } from './ziggy';

window.route = function(name, params=undefined){
    return route(name, params, undefined, Ziggy);
};