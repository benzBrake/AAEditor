export default class DetailsAnimator {
    constructor(el) {
        this.el = el;
        this.summary = el.querySelector('summary');
        this.content = el.querySelector('.details-content');
        this.animation = null;
        this.isClosing = false;
        this.isExpanding = false;
        this.summary.addEventListener('click', (e) => this.onClick(e));
    }

    onClick(e) {
        e.preventDefault();
        if (this.isClosing || this.isExpanding) {
            return;
        }

        if (this.el.open) {
            this.shrink();
        } else {
            this.expand();
        }
    }

    shrink() {
        this.isClosing = true;

        // 获取当前内容区域的高度作为动画起始点
        const startHeight = `${this.content.offsetHeight}px`;
        const endHeight = '0px';

        if (this.animation) {
            this.animation.cancel();
        }

        // 只对内容区域进行动画
        this.animation = this.content.animate({
            height: [startHeight, endHeight]
        }, {
            duration: 300,
            easing: 'ease-out'
        });

        this.animation.onfinish = () => {
            // 动画结束后，关闭 <details>
            this.el.removeAttribute('open');
            this.animation = null;
            this.isClosing = false;
            // 移除内联 height 样式，以便下次能正确计算
            this.content.style.height = '';
        };
    }

    expand() {
        this.isExpanding = true;
        const startHeight = '0px';

        // 先把 <details> 设为 open，这样才能计算出内容区的 scrollHeight
        this.el.setAttribute('open', '');

        // 使用 scrollHeight 获取内容完全展开后的高度（包括 padding）
        const endHeight = `${this.content.scrollHeight}px`;

        if (this.animation) {
            this.animation.cancel();
        }

        // 设置 overflow: hidden 防止内容在动画期间溢出
        this.content.style.overflow = 'hidden';

        // 对内容区域进行动画
        this.animation = this.content.animate({
            height: [startHeight, endHeight]
        }, {
            duration: 300,
            easing: 'ease-out'
        });

        this.animation.onfinish = () => {
            this.animation = null;
            this.isExpanding = false;
            // 动画结束后，移除内联样式，允许内容自适应高度
            this.content.style.height = '';
            this.content.style.overflow = '';
        };
    }
}
