import re

with open('src/scss/_components.scss', 'r') as f:
    content = f.read()

# Remove old empty state variations
content = re.sub(r'\.empty-content \{[^}]*\}', '', content)
content = re.sub(r'\.empty-state-dashed \{[^}]*\}', '', content)
content = re.sub(r'\.empty-state-card \{[^}]*\}', '', content)
content = re.sub(r'\.empty-enrolled-box \{[^}]*\}', '', content)
content = re.sub(r'\.empty-icon-lg \{[^}]*\}', '', content)

# The new beautiful empty state to replace the old .empty-state
new_empty_state = """
.empty-state {
  @include flex-center;
  flex-direction: column;
  padding: 60px 40px;
  background: var(--color-surface);
  border: 1px dashed var(--color-border);
  border-radius: 16px;
  text-align: center;
  color: var(--color-text-muted);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(var(--color-primary-rgb, 37,99,235), 0.08) 0%, transparent 70%);
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    border-radius: 50%;
    animation: floatGlow 6s ease-in-out infinite alternate;
  }

  p {
    font-size: 1.1rem;
    font-weight: 500;
    margin-top: 15px;
    position: relative;
    z-index: 1;
  }
}

@keyframes floatGlow {
  0% { transform: translate(-50%, 0) scale(1); }
  100% { transform: translate(-50%, 15px) scale(1.1); }
}

"""

# Replace the original .empty-state
content = re.sub(r'\.empty-state \{.*?(?=\n\n|\Z)', new_empty_state, content, flags=re.DOTALL)

with open('src/scss/_components.scss', 'w') as f:
    f.write(content)

print("Empty states modernized in SCSS.")
