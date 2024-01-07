var m=Object.defineProperty,p=Object.defineProperties;var c=Object.getOwnPropertyDescriptors;var i=Object.getOwnPropertySymbols;var u=Object.prototype.hasOwnProperty,d=Object.prototype.propertyIsEnumerable;var n=(s,e,t)=>e in s?m(s,e,{enumerable:!0,configurable:!0,writable:!0,value:t}):s[e]=t,l=(s,e)=>{for(var t in e||(e={}))u.call(e,t)&&n(s,t,e[t]);if(i)for(var t of i(e))d.call(e,t)&&n(s,t,e[t]);return s},o=(s,e)=>p(s,c(e));import{d as x,b as g,m as h,F as y,n as f,s as r}from"./app.js";import{g as v}from"./vform.es.js";import{O as _}from"./OpenFormFooter.js";import{B as b}from"./Breadcrumb.js";import{S as C}from"./SingleTemplate.js";const w=function(){r.commit("open/templates/startLoading"),r.dispatch("open/templates/loadIfEmpty").then(()=>{r.commit("open/templates/stopLoading")})},T={components:{Breadcrumb:b,OpenFormFooter:_,SingleTemplate:C},mixins:[x],beforeRouteEnter(s,e,t){w(),t()},data(){return{selectedIndustry:"all",searchTemplate:new v({search:""})}},mounted(){},computed:o(l(l({},g({authenticated:"auth/check",user:"auth/user"})),h({templates:s=>s["open/templates"].content,templatesLoading:s=>s["open/templates"].loading,industries:s=>s["open/templates"].industries,types:s=>s["open/templates"].types})),{breadcrumbs(){return this.type?[{route:{name:"templates"},label:"Templates"},{label:this.type.name}]:[{route:{name:"templates"},label:"Templates"}]},type(){return Object.values(this.types).find(s=>s.slug===this.$route.params.slug)},industriesOptions(){return[{name:"All Industries",value:"all"}].concat(Object.values(this.industries).map(s=>({name:s.name,value:s.slug})))},otherTypes(){return Object.values(this.types).filter(s=>s.slug!==this.$route.params.slug)},enrichedTemplates(){let s=this.templates;if(s=s.filter(a=>a.types&&a.types.length>0?a.types.includes(this.$route.params.slug):!1),this.selectedIndustry&&this.selectedIndustry!=="all"&&(s=s.filter(a=>a.industries&&a.industries.length>0?a.industries.includes(this.selectedIndustry):!1)),this.searchTemplate.search===""||this.searchTemplate.search===null)return s;const e={keys:["name","slug","description","short_description"]};return new y(s,e).search(this.searchTemplate.search).map(a=>a.item)},metaTitle(){return this.type?this.type.meta_title:"Form Template Type"},metaDescription(){return this.type?this.type.meta_description.substring(0,140):null}}),methods:{}};var k=function(){var e=this,t=e._self._c;return t("div",{staticClass:"flex flex-col min-h-full"},[t("breadcrumb",{attrs:{path:e.breadcrumbs}}),e.templatesLoading?t("div",{staticClass:"text-center my-4"},[t("loader",{staticClass:"h-6 w-6 text-nt-blue mx-auto"})],1):e.type===null||!e.type?t("p",{staticClass:"text-center my-4"},[e._v(" We could not find this type. ")]):[t("section",{staticClass:"py-12 sm:py-16 bg-gray-50 border-b border-gray-200"},[t("div",{staticClass:"px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"},[t("div",{staticClass:"text-center mx-auto"},[t("div",{staticClass:"font-semibold sm:w-full text-blue-500 mb-3"},[e._v(" "+e._s(e.type.name)+" ")]),t("h1",{staticClass:"text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900"},[e._v(" "+e._s(e.type.meta_title)+" ")]),t("p",{staticClass:"max-w-xl mx-auto text-gray-600 mt-4 text-lg font-normal"},[e._v(" "+e._s(e.type.meta_description)+" ")])])])]),t("section",{staticClass:"bg-white py-12 sm:py-16"},[t("div",{staticClass:"px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"},[t("div",{staticClass:"flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6 relative z-20"},[t("div",{staticClass:"flex items-center gap-4"},[t("div",{staticClass:"flex-1 sm:flex-none"},[t("select-input",{staticClass:"w-full sm:w-auto md:w-56",attrs:{name:"industry",options:e.industriesOptions},model:{value:e.selectedIndustry,callback:function(a){e.selectedIndustry=a},expression:"selectedIndustry"}})],1)]),t("div",{staticClass:"flex-1 w-full md:max-w-xs"},[t("text-input",{attrs:{name:"search",form:e.searchTemplate,placeholder:"Search..."}})],1)]),e.templatesLoading?t("div",{staticClass:"text-center mt-4"},[t("loader",{staticClass:"h-6 w-6 text-nt-blue mx-auto"})],1):e.enrichedTemplates.length===0?t("p",{staticClass:"text-center mt-4"},[e._v(" No templates found. ")]):t("div",{staticClass:"relative z-10"},[t("div",{staticClass:"grid grid-cols-1 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 sm:gap-y-12"},e._l(e.enrichedTemplates,function(a){return t("single-template",{key:a.id,attrs:{slug:a.slug}})}),1)])])]),t("section",{staticClass:"py-12 bg-white border-t border-gray-200 sm:py-16"},[t("div",{staticClass:"px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl"},[t("p",{staticClass:"text-gray-600 font-normal"},[e._v(" "+e._s(e.type.description)+" ")])])]),t("section",{staticClass:"py-12 bg-white border-t border-gray-200 sm:py-16"},[t("div",{staticClass:"px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl"},[t("div",{staticClass:"flex items-center justify-between"},[t("h4",{staticClass:"text-xl font-bold tracking-tight text-gray-900 sm:text-2xl"},[e._v(" Other Types ")]),t("v-button",{attrs:{to:{name:"templates"},color:"white",size:"small",arrow:!0}},[e._v(" View All Templates ")])],1),t("div",{staticClass:"grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"},e._l(e.otherTypes,function(a){return t("router-link",{key:a.slug,staticClass:"text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue",attrs:{to:{params:{slug:a.slug},name:"templates.types.show"},title:a.name}},[e._v(" "+e._s(a.name)+" ")])}),1)])])],t("open-form-footer",{staticClass:"mt-8 border-t"})],2)},I=[],O=f(T,k,I,!1,null,null,null,null);const A=O.exports;export{A as default};
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoidHlwZXMtc2hvdy5qcyIsInNvdXJjZXMiOlsiLi4vLi4vLi4vcmVzb3VyY2VzL2pzL3BhZ2VzL3RlbXBsYXRlcy90eXBlcy1zaG93LnZ1ZSJdLCJzb3VyY2VzQ29udGVudCI6WyI8dGVtcGxhdGU+XG4gIDxkaXYgY2xhc3M9XCJmbGV4IGZsZXgtY29sIG1pbi1oLWZ1bGxcIj5cbiAgICA8YnJlYWRjcnVtYiA6cGF0aD1cImJyZWFkY3J1bWJzXCIgLz5cblxuICAgIDxkaXYgdi1pZj1cInRlbXBsYXRlc0xvYWRpbmdcIiBjbGFzcz1cInRleHQtY2VudGVyIG15LTRcIj5cbiAgICAgIDxsb2FkZXIgY2xhc3M9XCJoLTYgdy02IHRleHQtbnQtYmx1ZSBteC1hdXRvXCIgLz5cbiAgICA8L2Rpdj5cbiAgICA8cCB2LWVsc2UtaWY9XCJ0eXBlID09PSBudWxsIHx8ICF0eXBlXCIgY2xhc3M9XCJ0ZXh0LWNlbnRlciBteS00XCI+XG4gICAgICBXZSBjb3VsZCBub3QgZmluZCB0aGlzIHR5cGUuXG4gICAgPC9wPlxuICAgIDx0ZW1wbGF0ZSB2LWVsc2U+XG4gICAgICA8c2VjdGlvbiBjbGFzcz1cInB5LTEyIHNtOnB5LTE2IGJnLWdyYXktNTAgYm9yZGVyLWIgYm9yZGVyLWdyYXktMjAwXCI+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJweC00IHNtOnB4LTYgbGc6cHgtOCBtYXgtdy03eGwgbXgtYXV0b1wiPlxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJ0ZXh0LWNlbnRlciBteC1hdXRvXCI+XG4gICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZm9udC1zZW1pYm9sZCBzbTp3LWZ1bGwgdGV4dC1ibHVlLTUwMCBtYi0zXCI+XG4gICAgICAgICAgICAgIHt7IHR5cGUubmFtZSB9fVxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgICA8aDEgY2xhc3M9XCJ0ZXh0LTN4bCBzbTp0ZXh0LTR4bCBsZzp0ZXh0LTV4bCBmb250LWJvbGQgdHJhY2tpbmctdGlnaHQgdGV4dC1ncmF5LTkwMFwiPlxuICAgICAgICAgICAgICB7eyB0eXBlLm1ldGFfdGl0bGUgfX1cbiAgICAgICAgICAgIDwvaDE+XG4gICAgICAgICAgICA8cCBjbGFzcz1cIm1heC13LXhsIG14LWF1dG8gdGV4dC1ncmF5LTYwMCBtdC00IHRleHQtbGcgZm9udC1ub3JtYWxcIj5cbiAgICAgICAgICAgICAge3sgdHlwZS5tZXRhX2Rlc2NyaXB0aW9uIH19XG4gICAgICAgICAgICA8L3A+XG4gICAgICAgICAgPC9kaXY+XG4gICAgICAgIDwvZGl2PlxuICAgICAgPC9zZWN0aW9uPlxuXG4gICAgICA8c2VjdGlvbiBjbGFzcz1cImJnLXdoaXRlIHB5LTEyIHNtOnB5LTE2XCI+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJweC00IHNtOnB4LTYgbGc6cHgtOCBtYXgtdy03eGwgbXgtYXV0b1wiPlxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJmbGV4IGZsZXgtY29sIHNtOmZsZXgtcm93IHNtOml0ZW1zLWNlbnRlciBzbTpqdXN0aWZ5LWJldHdlZW4gZ2FwLTQgc206Z2FwLTYgcmVsYXRpdmUgei0yMFwiPlxuICAgICAgICAgICAgPGRpdiBjbGFzcz1cImZsZXggaXRlbXMtY2VudGVyIGdhcC00XCI+XG4gICAgICAgICAgICAgIDxkaXYgY2xhc3M9XCJmbGV4LTEgc206ZmxleC1ub25lXCI+XG4gICAgICAgICAgICAgICAgPHNlbGVjdC1pbnB1dCB2LW1vZGVsPVwic2VsZWN0ZWRJbmR1c3RyeVwiIG5hbWU9XCJpbmR1c3RyeVwiXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgOm9wdGlvbnM9XCJpbmR1c3RyaWVzT3B0aW9uc1wiIGNsYXNzPVwidy1mdWxsIHNtOnctYXV0byBtZDp3LTU2XCJcbiAgICAgICAgICAgICAgICAvPlxuICAgICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPGRpdiBjbGFzcz1cImZsZXgtMSB3LWZ1bGwgbWQ6bWF4LXcteHNcIj5cbiAgICAgICAgICAgICAgPHRleHQtaW5wdXQgbmFtZT1cInNlYXJjaFwiIDpmb3JtPVwic2VhcmNoVGVtcGxhdGVcIiBwbGFjZWhvbGRlcj1cIlNlYXJjaC4uLlwiIC8+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXYgdi1pZj1cInRlbXBsYXRlc0xvYWRpbmdcIiBjbGFzcz1cInRleHQtY2VudGVyIG10LTRcIj5cbiAgICAgICAgICAgIDxsb2FkZXIgY2xhc3M9XCJoLTYgdy02IHRleHQtbnQtYmx1ZSBteC1hdXRvXCIgLz5cbiAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICA8cCB2LWVsc2UtaWY9XCJlbnJpY2hlZFRlbXBsYXRlcy5sZW5ndGggPT09IDBcIiBjbGFzcz1cInRleHQtY2VudGVyIG10LTRcIj5cbiAgICAgICAgICAgIE5vIHRlbXBsYXRlcyBmb3VuZC5cbiAgICAgICAgICA8L3A+XG4gICAgICAgICAgPGRpdiB2LWVsc2UgY2xhc3M9XCJyZWxhdGl2ZSB6LTEwXCI+XG4gICAgICAgICAgICA8ZGl2IGNsYXNzPVwiZ3JpZCBncmlkLWNvbHMtMSBtdC04IHNtOmdyaWQtY29scy0yIGxnOmdyaWQtY29scy0zIHhsOmdyaWQtY29scy00IGdhcC04IHNtOmdhcC15LTEyXCI+XG4gICAgICAgICAgICAgIDxzaW5nbGUtdGVtcGxhdGUgdi1mb3I9XCJ0ZW1wbGF0ZSBpbiBlbnJpY2hlZFRlbXBsYXRlc1wiIDprZXk9XCJ0ZW1wbGF0ZS5pZFwiIDpzbHVnPVwidGVtcGxhdGUuc2x1Z1wiIC8+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgPC9kaXY+XG4gICAgICA8L3NlY3Rpb24+XG5cbiAgICAgIDxzZWN0aW9uIGNsYXNzPVwicHktMTIgYmctd2hpdGUgYm9yZGVyLXQgYm9yZGVyLWdyYXktMjAwIHNtOnB5LTE2XCI+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJweC00IG14LWF1dG8gc206cHgtNiBsZzpweC04IG1heC13LTd4bFwiPlxuICAgICAgICAgIDxwIGNsYXNzPVwidGV4dC1ncmF5LTYwMCBmb250LW5vcm1hbFwiPlxuICAgICAgICAgICAge3sgdHlwZS5kZXNjcmlwdGlvbiB9fVxuICAgICAgICAgIDwvcD5cbiAgICAgICAgPC9kaXY+XG4gICAgICA8L3NlY3Rpb24+XG5cbiAgICAgIDxzZWN0aW9uIGNsYXNzPVwicHktMTIgYmctd2hpdGUgYm9yZGVyLXQgYm9yZGVyLWdyYXktMjAwIHNtOnB5LTE2XCI+XG4gICAgICAgIDxkaXYgY2xhc3M9XCJweC00IG14LWF1dG8gc206cHgtNiBsZzpweC04IG1heC13LTd4bFwiPlxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJmbGV4IGl0ZW1zLWNlbnRlciBqdXN0aWZ5LWJldHdlZW5cIj5cbiAgICAgICAgICAgIDxoNCBjbGFzcz1cInRleHQteGwgZm9udC1ib2xkIHRyYWNraW5nLXRpZ2h0IHRleHQtZ3JheS05MDAgc206dGV4dC0yeGxcIj5cbiAgICAgICAgICAgICAgT3RoZXIgVHlwZXNcbiAgICAgICAgICAgIDwvaDQ+XG5cbiAgICAgICAgICAgIDx2LWJ1dHRvbiA6dG89XCJ7bmFtZTondGVtcGxhdGVzJ31cIiBjb2xvcj1cIndoaXRlXCIgc2l6ZT1cInNtYWxsXCIgOmFycm93PVwidHJ1ZVwiPlxuICAgICAgICAgICAgICBWaWV3IEFsbCBUZW1wbGF0ZXNcbiAgICAgICAgICAgIDwvdi1idXR0b24+XG4gICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICA8ZGl2IGNsYXNzPVwiZ3JpZCBncmlkLWNvbHMtMSBnYXAtOCBtdC04IHNtOmdyaWQtY29scy0yIGxnOmdyaWQtY29scy0zIHhsOmdyaWQtY29scy00XCI+XG4gICAgICAgICAgICA8cm91dGVyLWxpbmsgdi1mb3I9XCJyb3cgaW4gb3RoZXJUeXBlc1wiIDprZXk9XCJyb3cuc2x1Z1wiIFxuICAgICAgICAgICAgICAgICAgICAgICAgOnRvPVwie3BhcmFtczp7c2x1Zzpyb3cuc2x1Z30sIG5hbWU6J3RlbXBsYXRlcy50eXBlcy5zaG93J31cIiBcbiAgICAgICAgICAgICAgICAgICAgICAgIDp0aXRsZT1cInJvdy5uYW1lXCJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNsYXNzPVwidGV4dC1ncmF5LTYwMCBkYXJrOnRleHQtZ3JheS00MDAgdHJhbnNpdGlvbi1jb2xvcnMgZHVyYXRpb24tMzAwIGhvdmVyOnRleHQtbnQtYmx1ZVwiXG4gICAgICAgICAgICA+XG4gICAgICAgICAgICAgIHt7IHJvdy5uYW1lIH19XG4gICAgICAgICAgICA8L3JvdXRlci1saW5rPlxuICAgICAgICAgIDwvZGl2PlxuICAgICAgICA8L2Rpdj5cbiAgICAgIDwvc2VjdGlvbj5cbiAgICAgIFxuICAgIDwvdGVtcGxhdGU+XG5cbiAgICA8b3Blbi1mb3JtLWZvb3RlciBjbGFzcz1cIm10LTggYm9yZGVyLXRcIi8+XG4gIDwvZGl2PlxuPC90ZW1wbGF0ZT5cblxuPHNjcmlwdD5cbmltcG9ydCBzdG9yZSBmcm9tICd+L3N0b3JlJ1xuaW1wb3J0IEZvcm0gZnJvbSAndmZvcm0nXG5pbXBvcnQgRnVzZSBmcm9tICdmdXNlLmpzJ1xuaW1wb3J0IHsgbWFwR2V0dGVycywgbWFwU3RhdGUgfSBmcm9tICd2dWV4J1xuaW1wb3J0IFNlb01ldGEgZnJvbSAnLi4vLi4vbWl4aW5zL3Nlby1tZXRhLmpzJ1xuaW1wb3J0IE9wZW5Gb3JtRm9vdGVyIGZyb20gJy4uLy4uL2NvbXBvbmVudHMvcGFnZXMvT3BlbkZvcm1Gb290ZXIudnVlJ1xuaW1wb3J0IEJyZWFkY3J1bWIgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9jb21tb24vQnJlYWRjcnVtYi52dWUnXG5pbXBvcnQgU2luZ2xlVGVtcGxhdGUgZnJvbSAnLi4vLi4vY29tcG9uZW50cy9wYWdlcy90ZW1wbGF0ZXMvU2luZ2xlVGVtcGxhdGUudnVlJ1xuXG5jb25zdCBsb2FkVGVtcGxhdGVzID0gZnVuY3Rpb24gKCkge1xuICBzdG9yZS5jb21taXQoJ29wZW4vdGVtcGxhdGVzL3N0YXJ0TG9hZGluZycpXG4gIHN0b3JlLmRpc3BhdGNoKCdvcGVuL3RlbXBsYXRlcy9sb2FkSWZFbXB0eScpLnRoZW4oKCkgPT4ge1xuICAgIHN0b3JlLmNvbW1pdCgnb3Blbi90ZW1wbGF0ZXMvc3RvcExvYWRpbmcnKVxuICB9KVxufVxuXG5leHBvcnQgZGVmYXVsdCB7XG4gIGNvbXBvbmVudHM6IHsgQnJlYWRjcnVtYiwgT3BlbkZvcm1Gb290ZXIsIFNpbmdsZVRlbXBsYXRlIH0sXG4gIG1peGluczogW1Nlb01ldGFdLFxuXG4gIGJlZm9yZVJvdXRlRW50ZXIgKHRvLCBmcm9tLCBuZXh0KSB7XG4gICAgbG9hZFRlbXBsYXRlcygpXG4gICAgbmV4dCgpXG4gIH0sXG5cbiAgZGF0YSAoKSB7XG4gICAgcmV0dXJuIHtcbiAgICAgIHNlbGVjdGVkSW5kdXN0cnk6ICdhbGwnLFxuICAgICAgc2VhcmNoVGVtcGxhdGU6IG5ldyBGb3JtKHtcbiAgICAgICAgc2VhcmNoOiAnJ1xuICAgICAgfSlcbiAgICB9XG4gIH0sXG5cbiAgbW91bnRlZCAoKSB7fSxcblxuICBjb21wdXRlZDoge1xuICAgIC4uLm1hcEdldHRlcnMoe1xuICAgICAgYXV0aGVudGljYXRlZDogJ2F1dGgvY2hlY2snLFxuICAgICAgdXNlcjogJ2F1dGgvdXNlcidcbiAgICB9KSxcbiAgICAuLi5tYXBTdGF0ZSh7XG4gICAgICB0ZW1wbGF0ZXM6IHN0YXRlID0+IHN0YXRlWydvcGVuL3RlbXBsYXRlcyddLmNvbnRlbnQsXG4gICAgICB0ZW1wbGF0ZXNMb2FkaW5nOiBzdGF0ZSA9PiBzdGF0ZVsnb3Blbi90ZW1wbGF0ZXMnXS5sb2FkaW5nLFxuICAgICAgaW5kdXN0cmllczogc3RhdGUgPT4gc3RhdGVbJ29wZW4vdGVtcGxhdGVzJ10uaW5kdXN0cmllcyxcbiAgICAgIHR5cGVzOiBzdGF0ZSA9PiBzdGF0ZVsnb3Blbi90ZW1wbGF0ZXMnXS50eXBlc1xuICAgIH0pLFxuICAgIGJyZWFkY3J1bWJzICgpIHtcbiAgICAgIGlmICghdGhpcy50eXBlKSB7XG4gICAgICAgIHJldHVybiBbeyByb3V0ZTogeyBuYW1lOiAndGVtcGxhdGVzJyB9LCBsYWJlbDogJ1RlbXBsYXRlcycgfV1cbiAgICAgIH1cbiAgICAgIHJldHVybiBbeyByb3V0ZTogeyBuYW1lOiAndGVtcGxhdGVzJyB9LCBsYWJlbDogJ1RlbXBsYXRlcycgfSwgeyBsYWJlbDogdGhpcy50eXBlLm5hbWUgfV1cbiAgICB9LFxuICAgIHR5cGUgKCkge1xuICAgICAgcmV0dXJuIE9iamVjdC52YWx1ZXModGhpcy50eXBlcykuZmluZCgodHlwZSkgPT4ge1xuICAgICAgICByZXR1cm4gdHlwZS5zbHVnID09PSB0aGlzLiRyb3V0ZS5wYXJhbXMuc2x1Z1xuICAgICAgfSlcbiAgICB9LFxuICAgIGluZHVzdHJpZXNPcHRpb25zICgpIHtcbiAgICAgIHJldHVybiBbeyBuYW1lOiAnQWxsIEluZHVzdHJpZXMnLCB2YWx1ZTogJ2FsbCcgfV0uY29uY2F0KE9iamVjdC52YWx1ZXModGhpcy5pbmR1c3RyaWVzKS5tYXAoKGluZHVzdHJ5KSA9PiB7XG4gICAgICAgIHJldHVybiB7XG4gICAgICAgICAgbmFtZTogaW5kdXN0cnkubmFtZSxcbiAgICAgICAgICB2YWx1ZTogaW5kdXN0cnkuc2x1Z1xuICAgICAgICB9XG4gICAgICB9KSlcbiAgICB9LFxuICAgIG90aGVyVHlwZXMoKSB7XG4gICAgICByZXR1cm4gT2JqZWN0LnZhbHVlcyh0aGlzLnR5cGVzKS5maWx0ZXIoKHR5cGUpID0+IHtcbiAgICAgICAgcmV0dXJuIHR5cGUuc2x1ZyAhPT0gdGhpcy4kcm91dGUucGFyYW1zLnNsdWcgXG4gICAgICB9KVxuICAgIH0sXG4gICAgZW5yaWNoZWRUZW1wbGF0ZXMgKCkge1xuICAgICAgbGV0IGVucmljaGVkVGVtcGxhdGVzID0gdGhpcy50ZW1wbGF0ZXNcblxuICAgICAgLy8gRmlsdGVyIGJ5IGN1cnJlbnQgVHlwZSBvbmx5XG4gICAgICBlbnJpY2hlZFRlbXBsYXRlcyA9IGVucmljaGVkVGVtcGxhdGVzLmZpbHRlcigoaXRlbSkgPT4ge1xuICAgICAgICByZXR1cm4gKGl0ZW0udHlwZXMgJiYgaXRlbS50eXBlcy5sZW5ndGggPiAwKSA/IGl0ZW0udHlwZXMuaW5jbHVkZXModGhpcy4kcm91dGUucGFyYW1zLnNsdWcpIDogZmFsc2VcbiAgICAgIH0pXG5cbiAgICAgIC8vIEZpbHRlciBieSBTZWxlY3RlZCBJbmR1c3RyeVxuICAgICAgaWYgKHRoaXMuc2VsZWN0ZWRJbmR1c3RyeSAmJiB0aGlzLnNlbGVjdGVkSW5kdXN0cnkgIT09ICdhbGwnKSB7XG4gICAgICAgIGVucmljaGVkVGVtcGxhdGVzID0gZW5yaWNoZWRUZW1wbGF0ZXMuZmlsdGVyKChpdGVtKSA9PiB7XG4gICAgICAgICAgcmV0dXJuIChpdGVtLmluZHVzdHJpZXMgJiYgaXRlbS5pbmR1c3RyaWVzLmxlbmd0aCA+IDApID8gaXRlbS5pbmR1c3RyaWVzLmluY2x1ZGVzKHRoaXMuc2VsZWN0ZWRJbmR1c3RyeSkgOiBmYWxzZVxuICAgICAgICB9KVxuICAgICAgfVxuXG4gICAgICBpZiAodGhpcy5zZWFyY2hUZW1wbGF0ZS5zZWFyY2ggPT09ICcnIHx8IHRoaXMuc2VhcmNoVGVtcGxhdGUuc2VhcmNoID09PSBudWxsKSB7XG4gICAgICAgIHJldHVybiBlbnJpY2hlZFRlbXBsYXRlc1xuICAgICAgfVxuXG4gICAgICAvLyBGdXplIHNlYXJjaFxuICAgICAgY29uc3QgZnV6ZU9wdGlvbnMgPSB7XG4gICAgICAgIGtleXM6IFtcbiAgICAgICAgICAnbmFtZScsXG4gICAgICAgICAgJ3NsdWcnLFxuICAgICAgICAgICdkZXNjcmlwdGlvbicsXG4gICAgICAgICAgJ3Nob3J0X2Rlc2NyaXB0aW9uJ1xuICAgICAgICBdXG4gICAgICB9XG4gICAgICBjb25zdCBmdXNlID0gbmV3IEZ1c2UoZW5yaWNoZWRUZW1wbGF0ZXMsIGZ1emVPcHRpb25zKVxuICAgICAgcmV0dXJuIGZ1c2Uuc2VhcmNoKHRoaXMuc2VhcmNoVGVtcGxhdGUuc2VhcmNoKS5tYXAoKHJlcykgPT4ge1xuICAgICAgICByZXR1cm4gcmVzLml0ZW1cbiAgICAgIH0pXG4gICAgfSxcbiAgICBtZXRhVGl0bGUgKCkge1xuICAgICAgcmV0dXJuIHRoaXMudHlwZSA/IHRoaXMudHlwZS5tZXRhX3RpdGxlIDogJ0Zvcm0gVGVtcGxhdGUgVHlwZSdcbiAgICB9LFxuICAgIG1ldGFEZXNjcmlwdGlvbiAoKSB7XG4gICAgICBpZiAoIXRoaXMudHlwZSkgcmV0dXJuIG51bGxcbiAgICAgIHJldHVybiB0aGlzLnR5cGUubWV0YV9kZXNjcmlwdGlvbi5zdWJzdHJpbmcoMCwgMTQwKVxuICAgIH1cbiAgfSxcblxuICBtZXRob2RzOiB7fVxufVxuPC9zY3JpcHQ+XG5cbjxzdHlsZSBsYW5nPSdzY3NzJz5cbi5uZi10ZXh0IHtcbiAgQGFwcGx5IHNwYWNlLXktNDtcbiAgaDIge1xuICAgIEBhcHBseSB0ZXh0LXNtIGZvbnQtbm9ybWFsIHRyYWNraW5nLXdpZGVzdCB0ZXh0LWdyYXktNTAwIHVwcGVyY2FzZTtcbiAgfVxuXG4gIHAge1xuICAgIEBhcHBseSBmb250LW5vcm1hbCBsZWFkaW5nLTcgdGV4dC1ncmF5LTkwMCBkYXJrOnRleHQtZ3JheS0xMDA7XG4gIH1cblxuICBvbCB7XG4gICAgQGFwcGx5IGxpc3QtZGVjaW1hbCBsaXN0LWluc2lkZTtcbiAgfVxuXG4gIHVsIHtcbiAgICBAYXBwbHkgbGlzdC1kaXNjIGxpc3QtaW5zaWRlO1xuICB9XG59XG48L3N0eWxlPlxuXG4iXSwibmFtZXMiOlsibG9hZFRlbXBsYXRlcyIsInN0b3JlIiwiX3NmY19tYWluIiwiQnJlYWRjcnVtYiIsIk9wZW5Gb3JtRm9vdGVyIiwiU2luZ2xlVGVtcGxhdGUiLCJTZW9NZXRhIiwidG8iLCJmcm9tIiwibmV4dCIsIkZvcm0iLCJfX3NwcmVhZFByb3BzIiwiX19zcHJlYWRWYWx1ZXMiLCJtYXBHZXR0ZXJzIiwibWFwU3RhdGUiLCJzdGF0ZSIsInR5cGUiLCJpbmR1c3RyeSIsImVucmljaGVkVGVtcGxhdGVzIiwiaXRlbSIsImZ1emVPcHRpb25zIiwiRnVzZSIsInJlcyJdLCJtYXBwaW5ncyI6Im1vQkF3R0EsTUFBQUEsRUFBQSxVQUFBLENBQ0FDLEVBQUEsT0FBQSw2QkFBQSxFQUNBQSxFQUFBLFNBQUEsNEJBQUEsRUFBQSxLQUFBLElBQUEsQ0FDQUEsRUFBQSxPQUFBLDRCQUFBLENBQ0EsQ0FBQSxDQUNBLEVBRUFDLEVBQUEsQ0FDQSxXQUFBLENBQUEsV0FBQUMsRUFBQSxlQUFBQyxFQUFBLGVBQUFDLENBQUEsRUFDQSxPQUFBLENBQUFDLENBQUEsRUFFQSxpQkFBQUMsRUFBQUMsRUFBQUMsRUFBQSxDQUNBVCxFQUFBLEVBQ0FTLEVBQUEsQ0FDQSxFQUVBLE1BQUEsQ0FDQSxNQUFBLENBQ0EsaUJBQUEsTUFDQSxlQUFBLElBQUFDLEVBQUEsQ0FDQSxPQUFBLEVBQ0EsQ0FBQSxDQUNBLENBQ0EsRUFFQSxTQUFBLENBQUEsRUFFQSxTQUFBQyxFQUFBQyxJQUFBLEdBQ0FDLEVBQUEsQ0FDQSxjQUFBLGFBQ0EsS0FBQSxXQUNBLENBQUEsR0FDQUMsRUFBQSxDQUNBLFVBQUFDLEdBQUFBLEVBQUEsZ0JBQUEsRUFBQSxRQUNBLGlCQUFBQSxHQUFBQSxFQUFBLGdCQUFBLEVBQUEsUUFDQSxXQUFBQSxHQUFBQSxFQUFBLGdCQUFBLEVBQUEsV0FDQSxNQUFBQSxHQUFBQSxFQUFBLGdCQUFBLEVBQUEsS0FDQSxDQUFBLEdBVkEsQ0FXQSxhQUFBLENBQ0EsT0FBQSxLQUFBLEtBR0EsQ0FBQSxDQUFBLE1BQUEsQ0FBQSxLQUFBLFdBQUEsRUFBQSxNQUFBLFdBQUEsRUFBQSxDQUFBLE1BQUEsS0FBQSxLQUFBLElBQUEsQ0FBQSxFQUZBLENBQUEsQ0FBQSxNQUFBLENBQUEsS0FBQSxhQUFBLE1BQUEsWUFBQSxDQUdBLEVBQ0EsTUFBQSxDQUNBLE9BQUEsT0FBQSxPQUFBLEtBQUEsS0FBQSxFQUFBLEtBQUFDLEdBQ0FBLEVBQUEsT0FBQSxLQUFBLE9BQUEsT0FBQSxJQUNBLENBQ0EsRUFDQSxtQkFBQSxDQUNBLE1BQUEsQ0FBQSxDQUFBLEtBQUEsaUJBQUEsTUFBQSxLQUFBLENBQUEsRUFBQSxPQUFBLE9BQUEsT0FBQSxLQUFBLFVBQUEsRUFBQSxJQUFBQyxJQUNBLENBQ0EsS0FBQUEsRUFBQSxLQUNBLE1BQUFBLEVBQUEsSUFDQSxFQUNBLENBQUEsQ0FDQSxFQUNBLFlBQUEsQ0FDQSxPQUFBLE9BQUEsT0FBQSxLQUFBLEtBQUEsRUFBQSxPQUFBRCxHQUNBQSxFQUFBLE9BQUEsS0FBQSxPQUFBLE9BQUEsSUFDQSxDQUNBLEVBQ0EsbUJBQUEsQ0FDQSxJQUFBRSxFQUFBLEtBQUEsVUFjQSxHQVhBQSxFQUFBQSxFQUFBLE9BQUFDLEdBQ0FBLEVBQUEsT0FBQUEsRUFBQSxNQUFBLE9BQUEsRUFBQUEsRUFBQSxNQUFBLFNBQUEsS0FBQSxPQUFBLE9BQUEsSUFBQSxFQUFBLEVBQ0EsRUFHQSxLQUFBLGtCQUFBLEtBQUEsbUJBQUEsUUFDQUQsRUFBQUEsRUFBQSxPQUFBQyxHQUNBQSxFQUFBLFlBQUFBLEVBQUEsV0FBQSxPQUFBLEVBQUFBLEVBQUEsV0FBQSxTQUFBLEtBQUEsZ0JBQUEsRUFBQSxFQUNBLEdBR0EsS0FBQSxlQUFBLFNBQUEsSUFBQSxLQUFBLGVBQUEsU0FBQSxLQUNBLE9BQUFELEVBSUEsTUFBQUUsRUFBQSxDQUNBLEtBQUEsQ0FDQSxPQUNBLE9BQ0EsY0FDQSxtQkFDQSxDQUNBLEVBRUEsT0FEQSxJQUFBQyxFQUFBSCxFQUFBRSxDQUFBLEVBQ0EsT0FBQSxLQUFBLGVBQUEsTUFBQSxFQUFBLElBQUFFLEdBQ0FBLEVBQUEsSUFDQSxDQUNBLEVBQ0EsV0FBQSxDQUNBLE9BQUEsS0FBQSxLQUFBLEtBQUEsS0FBQSxXQUFBLG9CQUNBLEVBQ0EsaUJBQUEsQ0FDQSxPQUFBLEtBQUEsS0FDQSxLQUFBLEtBQUEsaUJBQUEsVUFBQSxFQUFBLEdBQUEsRUFEQSxJQUVBLENBQ0EsR0FFQSxRQUFBLENBQUEsQ0FDQSJ9
