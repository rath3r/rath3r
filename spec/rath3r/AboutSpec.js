describe("About", function() {
  var expect = require('chai').expect;
  var should = require('chai').should();
  var sinon = require('sinon');
  var jquery = require('jquery');
  var jsdom = require('jsdom');
  const { JSDOM } = jsdom;
  var About = require('../../assets/scripts/About');

  var about = new About();

  beforeEach(function () {
      const dom = new JSDOM('');
      const about = new About();

      //jquery = require('jquery')(dom.window);
      $ = jquery(dom.window);
      //var spy = sinon.spy(jquery, "ajax");
  });

  afterEach(function () {
      jquery.ajax.restore();
   });

  it("should be initialise", function() {
    about.init();
    expect(about.initialised).to.equal(true);
  });

  it("should load a json string", function() {
    //var jsonData = about.loadJson();
    // foo.should.be.a('string');
    //jsonData.should.have.lengthOf(0);
    //jsonData.should.exist();
    //expect(about.loadJson).to.return([]);
  });

  it("should load a json string from an external source", function() {
    $.getJSON().spy();
    about.loadJson();


    sinon.assert.calledOnce(spy);
  });
});
