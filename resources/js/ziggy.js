const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"login":{"uri":"login","methods":["GET","HEAD"]},"logout":{"uri":"logout","methods":["POST"]},"register":{"uri":"register","methods":["GET","HEAD"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"dashboard.artifacts":{"uri":"dashboard\/artifacts","methods":["GET","HEAD"]},"dashboard.evaluations":{"uri":"dashboard\/evaluations","methods":["GET","HEAD"]},"dashboard.categories":{"uri":"dashboard\/categories","methods":["GET","HEAD"]},"dashboard.analytics":{"uri":"dashboard\/analytics","methods":["GET","HEAD"]},"home":{"uri":"home","methods":["GET","HEAD"]},"lang.switch":{"uri":"lang\/{locale}","methods":["GET","HEAD"],"parameters":["locale"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
