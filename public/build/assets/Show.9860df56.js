import{_ as o}from"./HeadlessAppLayout.f9b963f3.js";import{o as i,c as d,w as a,a as t,t as s}from"./app.99cc5bbd.js";const n=t("h2",{class:"font-semibold text-xl text-gray-800 leading-tight dark:text-white"}," Dashboard ",-1),r={class:"p-6 max-w-7xl mx-auto"},l={class:"grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-3"},c={class:"min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"},h={class:"p-4"},_=t("p",{class:"mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"}," Net Profit (VND) ",-1),m={class:"text-center text-lg font-semibold text-gray-700 dark:text-gray-200"},x={href:"/deals"},g={class:"text-green-500"},f={class:"min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"},u={class:"p-4"},b=t("p",{class:"mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"}," Fee & Income (VND) ",-1),p={class:"flex justify-between text-lg font-semibold text-gray-700 dark:text-gray-200"},w={class:"text-red-500 flex-shrink-0"},y={class:"text-green-500 flex-shrink-0"},k={class:"min-w-0 rounded-lg shadow-xs overflow-hidden bg-white dark:bg-gray-800"},v={class:"p-4"},N=t("p",{class:"mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"}," Deals & Uptime ",-1),D={class:"flex justify-between text-lg font-semibold text-gray-700 dark:text-gray-200"},C={class:"flex-shrink-0"},B={class:"flex-shrink-0"},T={__name:"Show",props:{netProfit:Number,fee:Number,dealsCount:Number,avgDuration:Number,initCapital:Number,upTime:String,incomes:Number},setup(e){return(P,S)=>(i(),d(o,{title:"Dashboard"},{header:a(()=>[n]),default:a(()=>[t("div",r,[t("div",l,[t("div",c,[t("div",h,[t("div",null,[_,t("p",m,[t("a",x,[t("span",g,"+"+s(e.netProfit)+" \u{1F4B0}",1)])])])])]),t("div",f,[t("div",u,[t("div",null,[b,t("p",p,[t("span",w,s(e.fee)+" \u{1F4B8}",1),t("span",y,s(e.incomes)+" \u{1F4B0}",1)])])])]),t("div",k,[t("div",v,[t("div",null,[N,t("p",D,[t("span",C,s(e.dealsCount)+" \u{1F91D}",1),t("span",B,s(e.upTime)+" \u23F1\uFE0F",1)])])])])])])]),_:1}))}};export{T as default};
