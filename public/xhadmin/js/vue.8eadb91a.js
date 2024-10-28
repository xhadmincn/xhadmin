import{az as m,N as d,S as u,aA as C,aB as E,aC as g,aD as a}from"./@vue.883d49f5.js";/**
* vue v3.4.23
* (c) 2018-present Yuxi (Evan) You and Vue contributors
* @license MIT
**/const f=new WeakMap;function h(e){let n=f.get(e??a);return n||(n=Object.create(null),f.set(e??a,n)),n}function T(e,n){if(!d(e))if(e.nodeType)e=e.innerHTML;else return u;const o=e,t=h(n),s=t[o];if(s)return s;if(e[0]==="#"){const c=document.querySelector(e);e=c?c.innerHTML:""}const r=C({hoistStatic:!0,onError:void 0,onWarn:u},n);!r.isCustomElement&&typeof customElements<"u"&&(r.isCustomElement=c=>!!customElements.get(c));const{code:l}=E(e,r),i=new Function("Vue",l)(g);return i._rc=!0,t[o]=i}m(T);
