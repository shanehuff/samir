import{_ as n}from"./HeadlessAppLayout.9751585d.js";import{o as e,c as d,w as o,a as t,e as a,i as c,F as l,t as r,f as _,g as x}from"./app.eb7d6da5.js";const m=t("div",{class:"flex justify-center"},[t("h2",{class:"text-xl text-gray-800 leading-tight dark:text-white"}," Daily ROI \u{1F4CA} ")],-1),h={class:"p-6 max-w-7xl mx-auto"},g={class:"grid gap-6 mb-8"},p={class:"min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"},f={class:"p-4 flex justify-between"},u={class:"text-sm font-semibold text-gray-400 dark:text-gray-400"},y={class:"text-sm font-semibold text-green-600 dark:text-green-300"},w={key:0},D={__name:"Show",props:{deals:Array},setup(i){return(k,b)=>(e(),d(n,{title:"Daily ROI"},{header:o(()=>[m]),default:o(()=>[t("div",h,[t("div",g,[(e(!0),a(l,null,c(i.deals,s=>(e(),a("div",p,[t("div",f,[t("span",u," \u{1F5D3} "+r(s.day),1),t("span",y,[parseInt(s.net_profit)>0?(e(),a("span",w,"+")):_("",!0),x(r(s.roi)+"% \u{1F4C8} ",1)])])]))),256))])])]),_:1}))}};export{D as default};
