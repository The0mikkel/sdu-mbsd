package math_expression;
public class Test30 {
	public int x;
	
	private External external;
	
	public Test30(External external) {
	    this.external = external;
	}
	
	public void compute() {
		x = this.external.sqrt(this.external.pow(this.external.pi(), 2));
	}
	public interface External {
		public int pow(int n0, int n1);
		public int sqrt(int n0);
		public int pi();
	}
}
